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

unique_names = {}

try:
    connection = mysql.connector.connect(**db_config)
    if connection.is_connected():
        cursor = connection.cursor()

        # Retrieve all existing scientific names from the database
        cursor.execute("SELECT scientific_name FROM trees")
        existing_names = set([row[0].strip().lower() for row in cursor.fetchall()])  # Use lower() to handle case insensitivity

        for index, row in df.iterrows():
            # Remove leading/trailing spaces and convert to lower case for comparison
            scientific_name = row['Scientific Name'].strip().lower()

            # Debugging: print name being processed
            print(f"Processing: {row['Scientific Name']}")

            # Check if the scientific name already exists in the database (case insensitive)
            if scientific_name not in existing_names:
                # If it doesn't exist, insert it without any suffix
                cursor.execute("""
                    INSERT INTO trees (common_name, scientific_name, PURL)
                    VALUES (%s, %s, %s)
                """, (row['Common Name'], row['Scientific Name'], row['PlantSoonURL']))
                existing_names.add(scientific_name)  # Add base name to the set
                print(f"Inserted base name: {row['Scientific Name']}")
            else:
                # If it exists, add a suffix (2), (3), etc.
                if scientific_name not in unique_names:
                    unique_names[scientific_name] = 1  # Start with (2) for next occurrences
                else:
                    unique_names[scientific_name] += 1
                modified_name = f"{row['Scientific Name']} ({unique_names[scientific_name]})"
                
                # Insert modified name with suffix
                cursor.execute("""
                    INSERT INTO trees (common_name, scientific_name, PURL)
                    VALUES (%s, %s, %s)
                """, (row['Common Name'], modified_name, row['PlantSoonURL']))
                print(f"Inserted modified name: {modified_name}")

        connection.commit()
        print("Data inserted successfully!")
except Error as e:
    print(f"Error: {e}")
finally:
    if connection.is_connected():
        cursor.close()
        connection.close()
