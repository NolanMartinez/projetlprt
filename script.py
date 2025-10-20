import random
import time
from datetime import datetime
import psycopg2

# === CONFIGURATION DE LA BASE DE DONNÉES ===
DB_CONFIG = {
    "host": "10.59.164.226",
    "dbname": "projet_gps",
    "user": "envoie",
    "password": "script",
    "port": 5432
}

# === REQUÊTES SQL ===
CREATE_TABLES_QUERY = """
CREATE TABLE IF NOT EXISTS capteur (
    id_capteur SERIAL PRIMARY KEY,
    nom VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS donnees (
    id_donnees INTEGER PRIMARY KEY,
    id_capteur INTEGER REFERENCES capteur(id_capteur),
    longitude DOUBLE PRECISION,
    latitude DOUBLE PRECISION,
    date_donnees TIMESTAMP
);
"""

INSERT_DONNEE_QUERY = """
INSERT INTO donnees (id_donnees, id_capteur, longitude, latitude, date_donnees)
VALUES (%s, %s, %s, %s, %s);
"""

def connect_db():
    conn = psycopg2.connect(**DB_CONFIG)
    conn.autocommit = True
    return conn, conn.cursor()

def generate_sensor_data():
    longitude = round(random.uniform(0, 600), 2)
    latitude = round(random.uniform(0, 589), 2)
    date_donnees = datetime.now()
    return longitude, latitude, date_donnees

def main():
    print("Connexion à la base PostgreSQL...")

    try:
        conn, cur = connect_db()
        cur.execute(CREATE_TABLES_QUERY)
        print("✅ Connexion réussie et tables vérifiées.\n")

        # Récupérer la liste des capteurs
        cur.execute("SELECT id_capteur, nom FROM capteur;")
        capteurs = cur.fetchall()
        if not capteurs:
            print("⚠️ Aucun capteur trouvé dans 'capteur'. Ajoutez-les avant de relancer.")
            return
        print(f"Capteurs détectés : {[c[1] for c in capteurs]}")

        # Récupérer le max(id_donnees) pour continuer l'incrémentation
        cur.execute("SELECT MAX(id_donnees) FROM donnees;")
        result = cur.fetchone()
        next_id = result[0] + 1 if result[0] is not None else 1

        print("Démarrage de la génération des données...\n")

        while True:
            for id_capteur, nom in capteurs:
                longitude, latitude, date_donnees = generate_sensor_data()
                cur.execute(INSERT_DONNEE_QUERY, (next_id, id_capteur, longitude, latitude, date_donnees))
                conn.commit()
                print(f"[{datetime.now().strftime('%H:%M:%S')}] {nom} → "
                      f"id_donnees={next_id}, longitude={longitude}, latitude={latitude}, date={date_donnees}")
                next_id += 1

            print("-" * 60)
            time.sleep(60)

    except Exception as e:
        print("❌ Erreur :", e)
    finally:
        if 'conn' in locals() and conn:
            conn.close()
            print("Connexion fermée.")

if __name__ == "__main__":
    main()
