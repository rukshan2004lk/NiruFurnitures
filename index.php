<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$pageTitle       = 'NiRu - Stylish Furniture For Modern Living';
$pageDescription = 'Curating comfort through artisanal craftsmanship and sustainable materials.';
$bodyClass       = 'home-page';

include 'includes/head.php';
include 'includes/navbar.php';

$heroImage = SITE_URL . '/assets/img/hero_img1.jpg';

$categories = [
    ['name' => 'Living Room', 'sub' => 'Browse Collection', 'href' => 'shop.php?category[]=living-room', 'class' => 'category-card-large', 'image' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=1100&q=90'],
    ['name' => 'Bedroom',     'sub' => '',                  'href' => 'shop.php?category[]=bedroom',     'class' => '',                    'image' => 'https://images.unsplash.com/photo-1617325247661-675ab4b64ae2?auto=format&fit=crop&w=700&q=90'],
    ['name' => 'Dining Room', 'sub' => '',                  'href' => 'shop.php?category[]=dining-room', 'class' => '',                    'image' => 'https://images.unsplash.com/photo-1615874959474-d609969a20ed?auto=format&fit=crop&w=700&q=90'],
    ['name' => 'Office',      'sub' => '',                  'href' => 'shop.php?category[]=office',      'class' => '',                    'image' => 'https://images.unsplash.com/photo-1593476550610-87baa860004a?auto=format&fit=crop&w=700&q=90'],
    ['name' => 'Outdoor',     'sub' => '',                  'href' => 'shop.php?category[]=outdoor',     'class' => '',                    'image' => 'https://images.unsplash.com/photo-1506439773649-6e0eb8cfb237?auto=format&fit=crop&w=700&q=90'],
];

$userWishlist = getUserWishlistIds();
$dbProducts = dbFetchAll("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.featured = 1 ORDER BY p.id DESC LIMIT 4");
$products = [];
foreach ($dbProducts as $dp) {
    $imgs = json_decode($dp['images'] ?? '[]', true);
    $imgUrl = productImageUrl($imgs[0] ?? null);
    
    $products[] = [
        'id'       => $dp['id'],
        'category' => $dp['category_name'] ?? 'Uncategorized',
        'name'     => $dp['name'],
        'price'    => formatPrice((float)$dp['price']),
        'href'     => 'product.php?id=' . $dp['id'],
        'image'    => $imgUrl
    ];
}

$testimonials = [
    ['initials' => 'AM', 'name' => 'Adrian Miller', 'role' => 'Architect, Oslo',          'rating' => 5,   'text' => '"The Nordic Lounge Chair is the centerpiece of my studio. The quality of the wood and the comfort of the cushions exceeded every expectation. It\'s functional art."'],
    ['initials' => 'SW', 'name' => 'Sarah White',   'role' => 'Interior Designer, London','rating' => 5,   'text' => '"NiRu has mastered the balance of minimalism and warmth. My living room feels so much more grounded since the modular sofa arrived. Delivery was seamless too."'],
    ['initials' => 'JK', 'name' => 'Julian Kang',   'role' => 'Creative Director, NYC',   'rating' => 4.5, 'text' => '"Beautiful craftsmanship. You can tell these pieces are made to last generations. The sustainable approach NiRu takes is why I keep coming back."'],
];

function renderHomeStars(float $rating): string
{
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($rating >= $i) {
            $html .= '<i class="bi bi-star-fill"></i>';
        } elseif ($rating >= $i - 0.5) {
            $html .= '<i class="bi bi-star-half"></i>';
        } else {
            $html .= '<i class="bi bi-star"></i>';
        }
    }
    return $html;
}
?>

<main>
    <section class="home-hero" aria-label="Stylish furniture for modern living">
        <img class="home-hero-bg" src="<?= e($heroImage) ?>" alt="Modern beige living room with sofa, plants, and lounge chairs">
        <div class="home-hero-card">
            <h1>Stylish Furniture<br>For Modern Living</h1>
            <p>Curating comfort through artisanal craftsmanship and sustainable materials. Discover a sanctuary designed for your home.</p>
            <div class="home-hero-actions">
                <a href="shop.php" class="btn home-btn-primary">Shop Now</a>
                <a href="#categories" class="btn home-btn-outline">Explore Categories</a>
            </div>
        </div>
    </section>

    <section class="home-section home-categories" id="categories">
        <div class="container-xl">
            <h2 class="home-section-title"><span>Shop</span> by Category</h2>
            <div class="home-category-grid">
                <?php foreach ($categories as $category): ?>
                <a class="home-category-card <?= e($category['class']) ?>" href="<?= e($category['href']) ?>">
                    <img src="<?= e($category['image']) ?>" alt="<?= e($category['name']) ?>">
                    <span class="home-category-shade"></span>
                    <span class="home-category-copy">
                        <strong><?= e($category['name']) ?></strong>
                        <?php if ($category['sub']): ?><small><?= e($category['sub']) ?></small><?php endif; ?>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="home-section home-featured" id="featured">
        <div class="container-xl">
            <div class="home-centered-heading">
                <h2>Featured Arrivals</h2>
                <p>Selected pieces that represent our philosophy of durability, comfort, and timeless aesthetics.</p>
            </div>

            <div class="home-product-grid">
                <?php foreach ($products as $product): ?>
                <article class="home-product-card">
                    <a href="<?= e($product['href']) ?>" class="home-product-image">
                        <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
                    </a>
                    <?php $isWishlisted = in_array($product['id'], $userWishlist); ?>
                    <button class="home-wishlist <?= $isWishlisted ? 'active' : '' ?>" type="button" aria-label="Add <?= e($product['name']) ?> to wishlist" 
                            data-action="toggle-wishlist" data-product-id="<?= e($product['id']) ?>">
                        <i class="bi <?= $isWishlisted ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                    </button>
                    <div class="home-product-body">
                        <span><?= e($product['category']) ?></span>
                        <h3><a href="<?= e($product['href']) ?>" class="text-dark text-decoration-none"><?= e($product['name']) ?></a></h3>
                        <p><?= e($product['price']) ?></p>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="home-section home-testimonials">
        <div class="container-xl">
            <div class="home-testimonial-head">
                <h2>Voices of Comfort</h2>
                <div class="home-testimonial-arrows" aria-hidden="true">
                    <span><i class="bi bi-arrow-left"></i></span>
                    <span><i class="bi bi-arrow-right"></i></span>
                </div>
            </div>

            <div class="home-testimonial-grid">
                <?php foreach ($testimonials as $testimonial): ?>
                <article class="home-testimonial-card">
                    <div class="home-stars"><?= renderHomeStars((float)$testimonial['rating']) ?></div>
                    <p><?= e($testimonial['text']) ?></p>
                    <div class="home-testimonial-author">
                        <span><?= e($testimonial['initials']) ?></span>
                        <div>
                            <strong><?= e($testimonial['name']) ?></strong>
                            <small><?= e($testimonial['role']) ?></small>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
