# Certificat SSL Aiven

## Instructions pour obtenir le certificat CA Aiven

1. Connectez-vous à votre tableau de bord Aiven
2. Allez dans votre service MySQL
3. Dans l'onglet "Overview" ou "Connection information", vous trouverez le **CA certificate**
4. Copiez le contenu du certificat (format PEM)

## Utilisation du certificat

### Option 1 : Sans certificat (recommandé pour commencer)
Laissez `DB_SSL_CA` vide dans votre fichier `.env`. La connexion fonctionnera avec `SSL_VERIFY_SERVER_CERT=false`.

### Option 2 : Avec certificat (plus sécurisé)
1. Créez un fichier `ca.pem` dans ce dossier (`config/ssl/ca.pem`)
2. Collez le contenu du certificat CA Aiven dans ce fichier
3. Mettez à jour votre `.env` :
   ```
   DB_SSL_CA=config/ssl/ca.pem
   AIVEN_SSL_CA=config/ssl/ca.pem
   ```

## Format du certificat

Le certificat doit être au format PEM, par exemple :
```
-----BEGIN CERTIFICATE-----
MIIDXTCCAkWgAwIBAgIJAKL7wQ...
...
-----END CERTIFICATE-----
```

## Note de sécurité

⚠️ Le fichier `ca.pem` contient des informations sensibles. Assurez-vous qu'il est dans `.gitignore` et ne sera pas commité dans Git.

