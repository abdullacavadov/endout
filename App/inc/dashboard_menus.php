<ul class="dashborad-menus">
    <li <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php' || basename($_SERVER['PHP_SELF']) == 'dashboard-user.php') ? 'class="active"' : ''; ?>>
        <a href="./dashboard">
            <i class="feather-grid"></i> <span>İdarəetmə paneli</span>
        </a>
    </li>
    <li>
        <a <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'class="active"' : ''; ?> href="./profile">
            <i class="fa-solid fa-user"></i> <span>Hesab</span>
        </a>
    </li>
    <?php if ($cust_type == 'partner') { ?>
        <li>
            <a <?php echo (basename($_SERVER['PHP_SELF']) == 'my-market.php') ? 'class="active"' : ''; ?> href="./my-market">
                <i class="fas fa-solid fa-store"></i> <span>Mağaza</span>
            </a>
        </li>
    <?php } ?>
    <?php if ($cust_type == 'partner') { ?>
        <li>
            <a <?php echo (basename($_SERVER['PHP_SELF']) == 'my-listings.php') ? 'class="active"' : ''; ?>
                href="./my-listings">
                <i class="feather-list"></i> <span>Elanlarım</span>
            </a>
        </li>
    <?php } ?>

    <li>
        <a <?php echo (basename($_SERVER['PHP_SELF']) == 'messages.php') ? 'class="active"' : ''; ?> href="./messages">
            <i class="fa-solid fa-comment-dots"></i> <span>Mesajlar</span>
        </a>
    </li>

    <li>
        <a <?php echo (basename($_SERVER['PHP_SELF']) == 'b2b.php') ? 'class="active"' : ''; ?> href="./b2b">
            <i class="fas fa-solid fa-star"></i> <span>Sorğular</span>
        </a>
    </li>
    <li>
        <a href="#" id="logoutBtn">
            <i class="fas fa-light fa-circle-arrow-left"></i> <span>Çıxış</span>
        </a>
    </li>
</ul>