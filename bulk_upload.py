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
        existing_names = set([row[0] for row in cursor.fetchall()])

        for index, row in df.iterrows():
            scientific_name = row['Scientific Name'].strip()

            # Check if the scientific name already exists in the database
            if scientific_name in existing_names:
                # If it exists, we append a suffix like (2), (3), etc.
                if scientific_name not in unique_names:
                    unique_names[scientific_name] = 1  # Start with (2) for next occurrences
                else:
                    unique_names[scientific_name] += 1
                scientific_name = f"{scientific_name} ({unique_names[scientific_name]})"
                df.at[index, 'Scientific Name'] = scientific_name

            # Add the base or modified scientific name to the set of existing names
            existing_names.add(scientific_name)
            
            common_name = row['Common Name']
            purl = row['PlantSoonURL']

            # Insert or update the entry in the database
            query = """
            INSERT INTO trees (common_name, scientific_name, PURL)
            VALUES (%s, %s, %s)
            ON DUPLICATE KEY UPDATE
            common_name = VALUES(common_name),
            scientific_name = VALUES(scientific_name),
            PURL = VALUES(PURL)
            """
            cursor.execute(query, (common_name, scientific_name, purl))
        
        connection.commit()
        print("Data inserted successfully!")
except Error as e:
    print(f"Error: {e}")
finally:
    if connection.is_connected():
        cursor.close()
        connection.close()
