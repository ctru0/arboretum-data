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

        # Retrieve all existing tree IDs from the database
        cursor.execute("SELECT tree_id FROM trees")
        existing_tree_ids = {row[0] for row in cursor.fetchall()}

        for index, row in df.iterrows():
            tree_id = row['tree_id']  # Assuming you have 'tree_id' in your CSV file
            scientific_name = row['Scientific Name'].strip()
            common_name = row['Common Name'].strip()

            # Debugging: print name being processed
            print(f"Processing: {scientific_name}")

            # Check if the tree_id already exists in the database
            if tree_id not in existing_tree_ids:
                # If tree_id does not exist, insert the record
                cursor.execute("""
                    INSERT INTO trees (tree_id, common_name, scientific_name, PURL)
                    VALUES (%s, %s, %s, %s)
                """, (tree_id, common_name, scientific_name, row['PlantSoonURL']))
                existing_tree_ids.add(tree_id)  # Add the tree_id to the set
                print(f"Inserted base name: {scientific_name}")
            else:
                # If tree_id exists, we may want to check the scientific name and update if needed
                if scientific_name not in unique_names:
                    unique_names[scientific_name] = 1  # Start with (2) for next occurrences
                else:
                    unique_names[scientific_name] += 1
                modified_name = f"{scientific_name} ({unique_names[scientific_name]})"
                
                # Insert modified name with suffix
                cursor.execute("""
                    INSERT INTO trees (tree_id, common_name, scientific_name, PURL)
                    VALUES (%s, %s, %s, %s)
                """, (tree_id, common_name, modified_name, row['PlantSoonURL']))
                print(f"Inserted modified name: {modified_name}")

        connection.commit()
        print("Data inserted successfully!")
except Error as e:
    print(f"Error: {e}")
finally:
    if connection.is_connected():
        cursor.close()
        connection.close()
