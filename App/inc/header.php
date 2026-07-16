<?php
require_once 'api/auth/auth_bootstrap.php';
?>

<header class="header header-nine">
    <div class="header-top">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <ul class="d-flex justify-content-start car-top-left gap-3">
                        <li class="d-flex align-items-center"><a href="<?= $base_url; ?>/markets"
                                style="border: none"><i class="fa-solid fa-store"></i> Mağazalar</a>
                        </li>
                        <li class="d-flex align-items-center"><a href="<?= $base_url; ?>/signup" style="border: none"><i
                                    class="fa-solid fa-business-time"></i> Partnyor ol</a></li>
                        <li class="d-flex align-items-center"><a href="<?= $base_url; ?>/bookmarks"
                                style="border: none"><i class="fa-solid fa-heart"></i> Favoritlər</a></li>

                    </ul>
                </div>
                <div class="col-md-4">
                    <div class="selection-list">
                        <div class="head-contact">
                            <?php if (!empty($_SESSION['customer_id'])): ?>
                                <a href="<?= $base_url; ?>/dashboard">
                                    <span class="me-2"><i class="feather-user"></i></span>
                                    <?php
                                    $stmt = $pdo->prepare("SELECT full_name FROM customers WHERE id = ?");
                                    $stmt->execute([(int) $_SESSION['customer_id']]);
                                    $fullName = $stmt->fetchColumn();
                                    echo htmlspecialchars($fullName);
                                    ?>
                                </a>
                            <?php else: ?>
                                <a href="<?= $base_url; ?>/login">
                                    <span class="me-2"><i class="feather-user"></i></span>Daxil ol
                                </a>
                            <?php endif; ?>



                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="container">
        <nav class="navbar navbar-expand-lg header-nav">
            <div class="navbar-header">
                <a id="mobile_btn" href="javascript:void(0);">
                    <span class="bar-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </a>
                <a href="<?= $base_url; ?>/home" class="navbar-brand logo">
                    <img src="<?= $base_url; ?>/assets/img/logo.png" class="img-fluid" alt="Logo" style="height: 38px;">
                </a>
                <a href="<?= $base_url; ?>/home" class="navbar-brand logo-small">
                    <img src="<?= $base_url; ?>/assets/img/logo.png" class="img-fluid" alt="Logo">
                </a>


            </div>
            <div class="main-menu-wrapper">
                <div class="menu-header">
                    <a href="<?= $base_url; ?>/home" class="menu-logo">
                        <img src="<?= $base_url; ?>/assets/img/logo.png" class="img-fluid" alt="Logo">
                    </a>
                    <a id="menu_close" class="menu-close" href="javascript:void(0);"> <i class="fas fa-times"></i></a>


                </div>
                <ul class="navbar-nav main-nav my-2 my-lg-0">

                    <li class="d-flex align-items-center me-5">
                        <button class="car-list-btn" id="catToggle" style="z-index: 9999;">
                            <i class="fas fa-list"></i>
                            <i class="fa-solid fa-xmark" style="display: none"></i>
                            <span>Kataloq</span>
                        </button>
                    </li>

                    <li class="has-submenu megamenu active">
                        <a href="<?= $base_url; ?>/home">Ana səhifə</a>

                    </li>

                    <?php
                    // MAIN
                    $type = $pdo->query("SELECT id, name, slug FROM listing_types ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

                    // sub
                    $mode = $pdo->query("SELECT id, slug, name FROM sale_modes ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

                    ?>

                    <?php foreach ($type as $t): ?>
                        <li class="has-submenu">
                            <a href="<?= $base_url; ?>/listings?lts=<?= $t['slug'] ?>">
                                <?= htmlspecialchars($t['name']) ?>
                                <i class="fas fa-chevron-down"></i>
                            </a>

                            <ul class="submenu">

                                <?php if ($t['id'] == 2): ?>
                                    <li class="">
                                        <a href="<?= $base_url; ?>/listings?lts=<?= $t['slug'] ?>&sms=perakende">
                                            Pərakəndə
                                        </a>
                                    </li>
                                <?php else: ?>

                                    <?php foreach ($mode as $m): ?>

                                        <li class="">
                                            <a href="<?= $base_url; ?>/listings?lts=<?= $t['slug'] ?>&sms=<?= $m['slug'] ?>">
                                                <?= htmlspecialchars($m['name']) ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>

                                <?php endif; ?>



                            </ul>

                        </li>
                    <?php endforeach; ?>


                </ul>
            </div>
            <div class="d-flex align-items-center block-e">

                <a href="<?= $base_url; ?>/add-listing" class="car-list-btn">
                    <span><i class="feather-plus-circle"></i></span>Yeni elan
                </a>


            </div>
        </nav>


    </div>

    <div id="menuOverlay" class="menu-overlay"></div>


    <?php
    $cats = $pdo->query("
            SELECT id, parent_id, name, slug, slug_path, icon
            FROM categories 
            ORDER BY parent_id, id
        ")->fetchAll(PDO::FETCH_ASSOC);

    // tree build
    $tree = [];
    $refs = [];

    foreach ($cats as $cat) {
        $cat['children'] = [];
        $refs[$cat['id']] = $cat;
    }

    foreach ($refs as $id => &$node) {
        if ($node['parent_id'] == null) {
            $tree[] = &$node;
        } else {
            $refs[$node['parent_id']]['children'][] = &$node;
        }
    }
    ?>

    <div id="categoryMenu" class="cat-menu">
        <div class="mega-menu">

            <ul class="level level-1"></ul>
            <ul class="level level-2"></ul>
            <ul class="level level-3"></ul>

        </div>
    </div>


    <style>
        .menu-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;

            background: rgba(0, 0, 0, 0.3);
            z-index: 998;
        }

        .menu-overlay.active {
            display: block;
        }

        .cat-menu {
            display: none;
            background-color: aliceblue;
            width: 100%;
            height: calc(100vh - 129px);
            position: fixed;
            top: 130px;
            left: 0;
            z-index: 999;
        }

        .cat-menu.active {
            display: block;
        }

        /* Desktop */
        @media (min-width: 768px) {
            .cat-menu {
                display: none;
            }

            .cat-menu.active {
                display: block;
            }
        }


        .mega-menu {
            display: flex;
            width: 900px;
            height: 100%;
            background: #fff;
        }

        .level {
            width: 300px;
            overflow-y: auto;
            border-right: 1px solid #eee;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .level li {
            padding: 7px;
            cursor: pointer;
            font-size: 14px;
        }

        .level li:hover,
        .level li.active {
            background: #f5f5f5;
        }

        .has-child::after {
            content: "›";
            float: right;
        }

        .level img {
            width: 20px;
            margin-right: 8px;
        }

        .level::-webkit-scrollbar {
            width: 6px;
        }

        .level::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
        }
    </style>

    <script>
        const categoryTree = <?= json_encode($tree, JSON_UNESCAPED_UNICODE) ?>;

        const level1 = document.querySelector('.level-1');
        const level2 = document.querySelector('.level-2');
        const level3 = document.querySelector('.level-3');

        function render(list, container, level) {
            container.innerHTML = '';

            list.forEach(item => {
                const li = document.createElement('li');

                li.innerHTML = `
            ${item.icon ? `<img src="<?= $base_url; ?>/assets/img/category/${item.icon}">` : ''}
            ${item.name}
        `;

                if (item.children && item.children.length) {
                    li.classList.add('has-child');
                }

                li.addEventListener('mouseenter', () => {
                    // active reset
                    container.querySelectorAll('li').forEach(x => x.classList.remove('active'));
                    li.classList.add('active');

                    if (level === 1) {
                        render(item.children || [], level2, 2);
                        level3.innerHTML = '';
                    }

                    if (level === 2) {
                        render(item.children || [], level3, 3);
                    }
                });

                li.addEventListener('click', () => {
                    window.location.href = "<?= $base_url ?>/listings/" + item.slug_path;
                });

                container.appendChild(li);
            });
        }

        // init
        render(categoryTree, level1, 1);

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('#catToggle');
            const menu = document.getElementById('categoryMenu');
            const overlay = document.getElementById('menuOverlay');
            const body = document.body;

            if (!menu) return;

            if (btn) {
                const iconOpen = btn.querySelector('.fa-list');
                const iconClose = btn.querySelector('.fa-xmark');

                menu.classList.toggle('active');
                const isOpen = menu.classList.contains('active');

                if (overlay) overlay.classList.toggle('active', isOpen);

                // ICON
                if (iconOpen && iconClose) { 
                    iconOpen.style.display = isOpen ? 'none' : 'inline'; 
                    iconClose.style.display = isOpen ? 'inline' : 'none'; 
                }

                //body.style.overflow = isOpen ? 'hidden' : '';

                return;
            }
        });

        document.querySelectorAll('.level li').forEach(li => {
            li.addEventListener('mouseenter', () => {

                // eyni level-də active-ləri sil
                const siblings = li.parentElement.querySelectorAll('li');
                siblings.forEach(s => s.classList.remove('active'));

                li.classList.add('active');
            });
        });
    </script>
</header>