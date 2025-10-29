<?php
// PHP logic for dynamic content on the About Us page
session_start();
require_once 'includes/header.php';
require_once 'includes/functions.php';
// Page Meta Information
$pageTitle = "About Us - Online Medical System";

// Hero Section Content
$heroTitle = "About Us";
$heroTagline = "23+ Years Experience in the Medical Industry. Care for Human Health and Help to Improve the Quality of Life.";
$heroImage = "assets/images/modern-facility.webp";

// Our Story/History Section Content
$ourStory = "Our Online Medical System was founded with a singular vision: to revolutionize healthcare access and delivery through technology. With over two decades of experience in the medical industry, our journey began by identifying key challenges in patient care and administrative efficiency. We've grown from a small initiative into a comprehensive platform, partnering with leading medical professionals and institutions to provide seamless, secure, and compassionate healthcare solutions. Our commitment is to leverage innovation to enhance the quality of life for every individual we serve.";

// Our Values Section Content
$ourValues = [
    [
        'title' => 'Integrity',
        'description' => 'Maintaining high ethical standards and transparency in all interactions and decisions is essential for building trust and confidence.'
    ],
    [
        'title' => 'Justice',
        'description' => 'Ensuring fairness and equity in access to healthcare and in the delivery of services.'
    ],
    [
        'title' => 'Innovation',
        'description' => 'Embracing new technologies and approaches to improve patient care and efficiency.'
    ],
    [
        'title' => 'Compassion',
        'description' => 'Treating every patient with empathy, dignity, and respect in all aspects of care.'
    ],
    [
        'title' => 'Excellence',
        'description' => 'Striving for the highest quality in medical services, technology, and patient outcomes.'
    ]
];

// Meet Our Team Section Content
$teamMembers = [
    [
        'name' => 'Dr. Sarah Johnson',
        'title' => 'Chief Medical Officer',
        'description' => 'Board-certified physician with 15 years of clinical experience and a passion for telemedicine innovation.',
        'image' => 'assets/images/team/sarah-johnson.jpeg'
    ],
    [
        'name' => 'Michael Chen',
        'title' => 'Technology Director',
        'description' => 'Healthcare IT specialist focused on creating secure, user-friendly medical platforms.',
        'image' => 'assets/images/team/micheal-chen.webp'
    ],
    [
        'name' => 'Dr. Amina Patel',
        'title' => 'Head of Patient Care',
        'description' => 'Dedicated to improving patient experiences through compassionate digital healthcare solutions.',
        'image' => 'assets/images/team/amina-patel.jpg'
    ],
    [
        'name' => 'Robert Williams',
        'title' => 'Operations Manager',
        'description' => 'Ensures seamless system operations and coordinates between medical and technical teams.',
        'image' => 'assets/images/team/robert-william.jpeg'
    ]
];

// HTML Structure for the About Us Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <main>
        <section class="hero-section" style="background-image: url('<?php echo $heroImage; ?>');">
            <div class="container">
                <h1><?php echo $heroTitle; ?></h1>
                <p><?php echo $heroTagline; ?></p>
            </div>
        </section>

        <section class="about-section">
            <div class="container">
                <h2>Our Story</h2>
                <p><?php echo $ourStory; ?></p>
            </div>
        </section>

        <section class="about-section" style="background-color: var(--background-light);">
            <div class="container">
                <h2>Our Values</h2>
                <ul class="values-list">
                    <?php foreach ($ourValues as $value): ?>
                        <li>
                            <i class="fas fa-check-circle icon-check"></i>
                            <div>
                                <h3><?php echo $value['title']; ?></h3>
                                <p><?php echo $value['description']; ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>

        <section class="about-section">
            <div class="container">
                <h2>Meet Our Team</h2>
                <div class="team-grid">
                    <?php foreach ($teamMembers as $member): ?>
                        <div class="team-card">
                            <img src="<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>" class="team-img">
                            <h3 class="team-name"><?php echo $member['name']; ?></h3>
                            <p class="team-title"><?php echo $member['title']; ?></p>
                            <p class="team-description"><?php echo $member['description']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>
</body>
</html>

<?php 
include_once 'includes/footer.php'; 
?>