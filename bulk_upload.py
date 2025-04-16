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

            # If the scientific name exists in the database or has been encountered before, increment the suffix
            if scientific_name in existing_names:
                if scientific_name not in unique_names:
                    unique_names[scientific_name] = 1
                else:
                    unique_names[scientific_name] += 1
                # Append (2), (3), etc.
                scientific_name = f"{scientific_name} ({unique_names[scientific_name]})"
                df.at[index, 'Scientific Name'] = scientific_name
            
            # Add the modified name to the set of existing names
            existing_names.add(scientific_name)
            
            common_name = row['Common Name']
            purl = row['PlantSoonURL']

            query = """
                INSERT INTO trees (common_name, scientific_name, PURL)
                VALUES (%s, %s, %s)
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
