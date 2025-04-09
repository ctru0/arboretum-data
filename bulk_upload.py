import pandas as pd
import mysql.connector
from mysql.connector import Error

db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': 'Password',
    'database': 'test'
}

csv_file = 'PlantSoon_Tree_URLs.csv'  
df = pd.read_csv(csv_file)

df.rename(columns={'PlantSoon URL': 'PlantSoonURL'}, inplace=True)

unique_names = {}
for index, row in df.iterrows():
    common_name = row['Common Name']
    if common_name in unique_names:
        unique_names[common_name] += 1
        df.at[index, 'Common Name'] = f"{common_name}{unique_names[common_name]}"
    else:
        unique_names[common_name] = 1


try:
    connection = mysql.connector.connect(**db_config)
    if connection.is_connected():
        cursor = connection.cursor()
        for index, row in df.iterrows():
            common_name = row['Common Name']
            scientific_name = row['Scientific Name']
            purl = row['PlantSoonURL']

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
