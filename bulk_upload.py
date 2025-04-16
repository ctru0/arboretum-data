import pandas as pd
import mysql.connector
from mysql.connector import Error

# Database configuration
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': 'Password',
    'database': 'ARBORETUM_DB'
}

# Load and clean CSV data
csv_file = 'PlantSoon_Tree_URLs.csv'  
df = pd.read_csv(csv_file)
df.rename(columns={'PlantSoon URL': 'PlantSoonURL'}, inplace=True)

# Track duplicates by scientific name
unique_names = {}

try:
    connection = mysql.connector.connect(**db_config)
    if connection.is_connected():
        cursor = connection.cursor()
        
        # Get existing scientific names from database
        cursor.execute("SELECT scientific_name FROM trees")
        existing_names = set([row[0] for row in cursor.fetchall()])

        for index, row in df.iterrows():
            original_scientific_name = row['Scientific Name'].strip()
            scientific_name = original_scientific_name

            # Check and handle duplicates
            if scientific_name in existing_names:
                if scientific_name not in unique_names:
                    unique_names[scientific_name] = 1
                else:
                    unique_names[scientific_name] += 1
                scientific_name = f"{original_scientific_name} ({unique_names[scientific_name]})"
                df.at[index, 'Scientific Name'] = scientific_name

            # Add the (possibly modified) name to the set
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
