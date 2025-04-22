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




# Load entry data from labkey.csv
labkey_file = 'labkey.csv'
entries_df = pd.read_csv(labkey_file)

try:
    connection = mysql.connector.connect(**db_config)
    if connection.is_connected():
        cursor = connection.cursor(buffered=True)

        for index, row in entries_df.iterrows():
            common_name = str(row['Common Name']).strip()

            # Get TREE_ID from common name (assume first encountered instance)
            cursor.execute("SELECT TREE_ID FROM TREES WHERE COMMON_NAME = %s LIMIT 1", (common_name,))
            result = cursor.fetchone()

            if result:
                tree_id = result[0]

                # --- Insert 2023 entry ---
                try:
                    h_2023 = float(row['2023 Tree Height (m).'])
                    c_2023 = float(row['2023 Circumference (cm).'])

                    insert_2023 = """
                    INSERT INTO ENTRIES (TREE_ID, HEIGHT_1, HEIGHT_2, HEIGHT_3, CIRCUMFERENCE, NETID, DATE_SUBMITTED)
                    VALUES (%s, %s, %s, %s, %s, %s, %s)
                    """
                    cursor.execute(insert_2023, (
                        tree_id,
                        h_2023, h_2023, h_2023,
                        c_2023,
                        'esulli4',
                        '2023-12-31 23:59:00'
                    ))
                except (ValueError, KeyError):
                    pass  # Skip if 2023 data is missing or invalid

                # --- Insert 2024 entry ---
                try:
                    h_2024 = float(row['2024 Tree Height (m)'])  # Updated column name without period
                    c_2024 = float(row['2024 Circumference (cm)'])  # Updated column name without period
    
                    print(f"2024 Data: Height = {h_2024}, Circumference = {c_2024}")  # Debugging output

                    insert_2024 = """
                    INSERT INTO ENTRIES (TREE_ID, HEIGHT_1, HEIGHT_2, HEIGHT_3, CIRCUMFERENCE, NETID, DATE_SUBMITTED)
                    VALUES (%s, %s, %s, %s, %s, %s, %s)
                    """
                    # Debugging print statement before execution
                    print(f"Executing query for 2024 entry with tree_id: {tree_id}, height: {h_2024}, circumference: {c_2024}")

                    cursor.execute(insert_2024, (
                        tree_id,
                        h_2024, h_2024, h_2024,
                        c_2024,
                        'esulli4',
                        '2024-12-31 23:59:00'
                ))

                except (ValueError, KeyError) as e:
                    print(f"Skipping 2024 entry due to error: {e}")  # Error output for debugging
                    pass  # Skip if 2024 data is missing or invalid



        connection.commit()
        print("Labkey 2023 and 2024 entries inserted into ENTRIES table successfully!")

except Error as e:
    print(f"Error inserting labkey entries: {e}")

finally:
    if 'cursor' in locals() and cursor:
        cursor.close()
    if 'connection' in locals() and connection.is_connected():
        connection.close()