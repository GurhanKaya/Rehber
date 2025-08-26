# 🐳 Docker ile Reh3 Projesi

Bu proje Docker kullanarak çalıştırılabilir.

## 📋 Gereksinimler

- Docker Desktop
- Docker Compose

## 🚀 Hızlı Başlangıç

### 1. Projeyi Klonlayın
```bash
git clone <repository-url>
cd reh3
```

### 2. Environment Dosyasını Oluşturun
```bash
cp .env.example .env
```

### 3. Docker Container'larını Başlatın
```bash
docker-compose up -d
```

### 4. Uygulamaya Erişin
- **Web Uygulaması**: http://localhost:8000
- **Redis**: localhost:6379

## 🔧 Geliştirme Ortamı

### Container'ları Başlatma
```bash
# Tüm servisleri başlat
docker-compose up -d

# Logları görüntüle
docker-compose logs -f

# Belirli bir servisin loglarını görüntüle
docker-compose logs -f app
```

### Container'a Bağlanma
```bash
# PHP container'ına bağlan
docker-compose exec app bash

# SQLite veritabanına bağlan
docker-compose exec app sqlite3 database/database.sqlite
```

### Composer Komutları
```bash
# Composer install
docker-compose exec app composer install

# Yeni paket ekle
docker-compose exec app composer require package-name

# Autoload'u yenile
docker-compose exec app composer dump-autoload
```

### Artisan Komutları
```bash
# Migration çalıştır
docker-compose exec app php artisan migrate

# Cache temizle
docker-compose exec app php artisan cache:clear

# Config cache
docker-compose exec app php artisan config:cache

# Route cache
docker-compose exec app php artisan route:cache

# View cache
docker-compose exec app php artisan view:cache
```

### NPM Komutları
```bash
# Node modules yükle
docker-compose exec app npm install

# Development build
docker-compose exec app npm run dev

# Production build
docker-compose exec app npm run build
```

## 🛠️ Sorun Giderme

### Container'lar Çalışmıyor
```bash
# Container durumunu kontrol et
docker-compose ps

# Container'ları yeniden başlat
docker-compose restart

# Container'ları durdur ve sil
docker-compose down
docker-compose up -d
```

### SQLite Veritabanı Sorunu
```bash
# SQLite dosyasının varlığını kontrol et
docker-compose exec app ls -la database/

# SQLite dosyasını yeniden oluştur
docker-compose exec app touch database/database.sqlite

# Migration'ları yeniden çalıştır
docker-compose exec app php artisan migrate:fresh --seed
```

### Permission Sorunları
```bash
# Storage klasörü izinlerini düzelt
docker-compose exec app chmod -R 775 storage
docker-compose exec app chmod -R 775 bootstrap/cache
docker-compose exec app chmod -R 775 database
```

## 🚀 Production Deployment

### 1. Environment Dosyasını Hazırlayın
```bash
cp .env.example .env
# .env dosyasını production ayarlarıyla düzenleyin
```

### 2. Production Build
```bash
# Production için build
docker-compose -f docker-compose.prod.yml up -d --build
```

### 3. SSL Sertifikası (Opsiyonel)
```bash
# SSL sertifikalarını docker/nginx/ssl/ klasörüne koyun
# Nginx konfigürasyonunu güncelleyin
```

## 📁 Docker Dosya Yapısı

```
docker/
├── nginx/
│   └── conf.d/
│       └── app.conf
└── php/
    └── local.ini
```

## 🔧 Konfigürasyon

### PHP Ayarları
`docker/php/local.ini` dosyasında PHP ayarlarını değiştirebilirsiniz.

### Nginx Ayarları
`docker/nginx/conf.d/app.conf` dosyasında Nginx ayarlarını değiştirebilirsiniz.

## 🐛 Yaygın Sorunlar

### Windows'ta Permission Sorunu
```bash
# Windows'ta WSL2 kullanıyorsanız
# .env dosyasında COMPOSE_CONVERT_WINDOWS_EPOCH=1 ekleyin
```

### Port Çakışması
```bash
# docker-compose.yml dosyasında portları değiştirin
ports:
  - "8080:80"  # 8000 yerine 8080 kullanın
```

### Memory Sorunu
```bash
# Docker Desktop'ta memory limitini artırın
# Settings > Resources > Memory: 4GB+
```

### SQLite Dosya Sorunu
```bash
# SQLite dosyasının yazma izinlerini kontrol edin
docker-compose exec app chmod 666 database/database.sqlite
```

## 📞 Destek

Sorun yaşarsanız:
1. `docker-compose logs` komutunu çalıştırın
2. Container durumunu kontrol edin: `docker-compose ps`
3. Issue açın veya destek ekibine başvurun 