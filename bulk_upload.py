import pandas as pd
import mysql.connector
from mysql.connector import Error

db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': 'Password',
    'database': 'ARBORETUM_DB'
}

csv_file = 'PlantSoon_Tree_URLs.csv'  
df = pd.read_csv(csv_file)

df.rename(columns={'PlantSoon URL': 'PlantSoonURL'}, inplace=True)

# Dictionary to keep track of duplicate scientific names
unique_names = {}

try:
    connection = mysql.connector.connect(**db_config)
    if connection.is_connected():
        cursor = connection.cursor()

        for index, row in df.iterrows():
            scientific_name = row['Scientific Name'].strip()
            common_name = row['Common Name'].strip()

            # Debugging: print name being processed
            print(f"Processing: {scientific_name}")

            # Check if the scientific name already exists in the database
            cursor.execute("SELECT tree_id FROM trees WHERE scientific_name = %s", (scientific_name,))
            existing = cursor.fetchone()

            if not existing:
                # If no existing record, insert the record without worrying about tree_id
                cursor.execute("""
                    INSERT INTO trees (common_name, scientific_name, PURL)
                    VALUES (%s, %s, %s)
                """, (common_name, scientific_name, row['PlantSoonURL']))
                print(f"Inserted base name: {scientific_name}")
            else:
                # If scientific name exists, add suffix (2), (3), etc.
                if scientific_name not in unique_names:
                    unique_names[scientific_name] = 1
                else:
                    unique_names[scientific_name] += 1
                modified_name = f"{scientific_name} ({unique_names[scientific_name]})"
                
                # Insert modified name with suffix
                cursor.execute("""
                    INSERT INTO trees (common_name, scientific_name, PURL)
                    VALUES (%s, %s, %s)
                """, (common_name, modified_name, row['PlantSoonURL']))
                print(f"Inserted modified name: {modified_name}")

        connection.commit()
        print("Data inserted successfully!")
except Error as e:
    print(f"Error: {e}")
finally:
    if connection.is_connected():
        cursor.close()
        connection.close()
