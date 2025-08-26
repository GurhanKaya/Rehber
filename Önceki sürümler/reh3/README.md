# Reh3 - Laravel Livewire Task Management System

Modern bir görev yönetim sistemi olan Reh3, Laravel ve Livewire kullanılarak geliştirilmiştir. Bu sistem randevu yönetimi, görev takibi ve kullanıcı yönetimi özelliklerini içerir.

## 🚀 Özellikler

- **Kullanıcı Yönetimi**: Kayıt, giriş, profil yönetimi
- **Randevu Sistemi**: Randevu oluşturma, düzenleme ve takip
- **Görev Yönetimi**: Görev oluşturma, atama, takip ve dosya yükleme
- **Personel Paneli**: Personel için özel yönetim arayüzü
- **Admin Paneli**: Tam yönetim kontrolü
- **AI Chat Widget**: Yapay zeka destekli sohbet özelliği
- **Çok Dilli Destek**: Türkçe ve İngilizce dil desteği

## 📋 Gereksinimler

- PHP 8.2+
- Composer
- Node.js & NPM
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

### 6. Storage Linkini Oluşturun
```bash
php artisan storage:link
```

### 7. Asset'leri Derleyin
```bash
npm run build
```

### 8. Uygulamayı Çalıştırın
```bash
php artisan serve
```

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

## 🔒 Güvenlik

- Tüm kullanıcı girişleri doğrulanır
- CSRF koruması aktif
- SQL injection koruması
- XSS koruması
- Dosya yükleme güvenliği

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
