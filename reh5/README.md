# Reh3 - Laravel Livewire Task Management System

Modern bir görev yönetim sistemi olan Reh3, Laravel ve Livewire kullanılarak geliştirilmiştir. Bu sistem randevu yönetimi, görev takibi ve kullanıcı yönetimi özelliklerini içerir.

## 🚀 Özellikler

- **Kullanıcı Yönetimi**: Kayıt, giriş, profil yönetimi
- **Randevu Sistemi**: Randevu oluşturma, düzenleme ve takip
- **Görev Yönetimi**: Görev oluşturma, atama, takip ve dosya yükleme
- **Personel Paneli**: Personel için özel yönetim arayüzü
- **Admin Paneli**: Tam yönetim kontrolü
- **Çok Dilli Destek**: Türkçe ve İngilizce dil desteği

- **Departman & Unvan Yönetimi**: Departman ve departmana bağlı unvan yapısı
- **Bildirim & E-posta Kuyruğu**: E-posta ve bildirim işlemleri çoklu kuyruklarla (default, emails, notifications, tasks) arka planda
- **Kayıt & Loglar**: Görev yorumları, dosyaları ve log kaydı

## 📋 Gereksinimler

- PHP 8.2+
- Composer
- Node.js & NPM (Vite + Tailwind)
- MySQL/PostgreSQL/SQLite
- Docker (opsiyonel)

## 🛠️ Kurulum

### 1. Projeyi Klonlayın
```bash
git clone https://github.com/your-username/reh3.git
cd reh3
```

### 2. Bağımlılıkları Yükleyin
```bash
composer install
npm install
```

### 3. Ortam Dosyasını Yapılandırın
```bash
cp .env.example .env
php artisan key:generate
```

Minimum .env ayarları:
```env
# Uygulama
APP_NAME=Reh3
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

# Veritabanı
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reh3
DB_USERNAME=root
DB_PASSWORD=

# Queue (veritabanı kuyruğu önerilir)
QUEUE_CONNECTION=database

# Mail (örnek SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="no-reply@example.com"
MAIL_FROM_NAME="Reh3"
```

### 4. Veritabanını Yapılandırın
`.env` dosyasında veritabanı ayarlarınızı yapılandırın:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reh3
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Veritabanını Oluşturun
```bash
php artisan migrate
php artisan db:seed
```

Notlar:
- Seeder, varsayılan departmanları ve bu departmanlara bağlı unvanları oluşturur.
- Ardından 5 admin ve 25 personel kullanıcı üretir. Varsayılan şifre: `password`.
- Admin e-postasını öğrenmek için:
```bash
php artisan tinker --execute="App\\Models\\User::where('role','admin')->first(['email'])"
```

### 6. Storage Linkini Oluşturun
```bash
php artisan storage:link
```

### 7. Asset'leri Derleyin
```bash
npm run build
```

Geliştirme sırasında HMR için:
```bash
npm run dev
```

### 8. Queue Sistemini Kurun (Queue ile Arka Plan İşleri)
```bash
# Queue tablosunu oluşturun
php artisan queue:table
php artisan migrate

# Queue worker'ı başlatın (YENİ TERMİNAL PENCERESİNDE)
php artisan queue:work --queue=default,emails,notifications,tasks --sleep=3 --tries=3
```

### 9. Uygulamayı Çalıştırın
```bash
# Terminal 1: Laravel projesini başlat
php artisan serve

# Terminal 2: Queue worker'ı başlat (e-posta gönderimi için)
php artisan queue:work --queue=default,emails,notifications,tasks --sleep=3 --tries=3
```

**⚠️ ÖNEMLİ:** E-posta gönderimi için her iki komutu da çalıştırmanız gerekiyor!

## 🐳 Docker ile Kurulum

### Docker Compose ile Hızlı Başlangıç
```bash
docker-compose up -d
```

Docker kurulumu için detaylı bilgi: [README-Docker.md](README-Docker.md)

## 📁 Proje Yapısı

```
reh3/
├── app/
│   ├── Http/Controllers/     # HTTP Kontrolcüleri
│   ├── Livewire/            # Livewire Bileşenleri
│   │   ├── Admin/          # Admin Paneli
│   │   ├── Auth/           # Kimlik Doğrulama
│   │   ├── Guest/          # Misafir Sayfaları
│   │   ├── Personel/       # Personel Paneli
│   │   └── Settings/       # Ayarlar
│   ├── Services/            # Queue, Cache, Performans hizmetleri
│   ├── Models/             # Eloquent Modelleri
│   └── Notifications/      # Bildirimler
├── database/
│   ├── migrations/         # Veritabanı Migrasyonları
│   └── seeders/           # Veritabanı Seed'leri
├── resources/
│   ├── views/             # Blade Şablonları
│   ├── css/              # CSS Dosyaları
│   └── js/               # JavaScript Dosyaları
└── routes/               # Rota Tanımları
```

## 🔧 Kullanım

### Admin Paneli
- Kullanıcı yönetimi
- Görev oluşturma ve atama
- Randevu yönetimi
- Sistem ayarları

### Personel Paneli
- Görev görüntüleme ve güncelleme
- Randevu takibi
- Profil yönetimi

### Misafir Sayfaları
- Randevu rezervasyonu
- Kullanıcı arama

## 🧪 Test

```bash
# Tüm testleri çalıştır
php artisan test

# Belirli bir test dosyasını çalıştır
php artisan test tests/Feature/Auth/AuthenticationTest.php
```

## 📝 Geliştirme

### Yeni Özellik Ekleme
1. Yeni migration oluşturun: `php artisan make:migration create_new_table`
2. Model oluşturun: `php artisan make:model NewModel`
3. Livewire bileşeni oluşturun: `php artisan make:livewire NewComponent`
4. View dosyası oluşturun: `resources/views/livewire/new-component.blade.php`

### Kod Stili
```bash
# PHP kod stilini düzelt
./vendor/bin/pint

# JavaScript kod stilini düzelt
npm run lint
```

### Livewire, Volt ve Vite
- Livewire bileşenleri `resources/views/livewire` altında.
- Volt mount ayarları `App\\Providers\\VoltServiceProvider` içinde tanımlıdır.
- Vite, `resources/css/app.css` ve `resources/js/app.js` giriş dosyalarını kullanır. HMR için `npm run dev` çalıştırın.

## 🔒 Güvenlik

- Tüm kullanıcı girişleri doğrulanır
- CSRF koruması aktif
- SQL injection koruması
- XSS koruması
- Dosya yükleme güvenliği

## 📧 Queue Sistemi (E-posta Gönderimi)

### Queue Worker Yönetimi
```bash
# Queue worker'ı başlat (çoklu kuyruk)
php artisan queue:work --queue=default,emails,notifications,tasks --sleep=3 --tries=3

# Queue worker'ı durdur
php artisan queue:restart

# Queue durumunu kontrol et
php artisan queue:failed

# Failed job'ları tekrar dene
php artisan queue:retry all
```

### Queue Sistemi Avantajları
- ✅ E-posta gönderimi 10-100x daha hızlı
- ✅ Kullanıcı beklemeyecek
- ✅ Background'da işlenir
- ✅ SMTP timeout'ları azalır
- ✅ Sistem performansı artar

### ⚠️ Önemli Notlar
- **Her proje başlangıcında queue worker'ı başlatın**
- **Queue worker olmadan e-postalar yavaş gönderilir**
- **İki terminal penceresi gerekli:**
  - Terminal 1: `php artisan serve`
  - Terminal 2: `php artisan queue:work --queue=default,emails,notifications,tasks --sleep=3 --tries=3`

Prod ortamı için Supervisor kullanmanız önerilir (örnek):
```ini
[program:reh3-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work --queue=default,emails,notifications,tasks --sleep=3 --tries=3 --timeout=120
autostart=true
autorestart=true
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/supervisor/reh3-queue.log
```

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için [LICENSE](LICENSE) dosyasına bakın.

## 🤝 Katkıda Bulunma

1. Bu repository'yi fork edin
2. Yeni bir branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add some amazing feature'`)
4. Branch'inizi push edin (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## 📞 İletişim

- Proje Sahibi: [Your Name]
- Email: [your-email@example.com]
- GitHub: [@your-username]

## 🙏 Teşekkürler

- [Laravel](https://laravel.com) - PHP Framework
- [Livewire](https://livewire.laravel.com) - Full-stack Framework
- [Flux](https://flux.laravel.com) - UI Components
- [Tailwind CSS](https://tailwindcss.com) - CSS Framework
