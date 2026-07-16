<?php
require_once __DIR__ . "/inc/config.php";
require_login($pdo);
require_verify($pdo, 'verify-phone');

require_once __DIR__ . "/api/data/market_data.php";
require_once __DIR__ . "/api/data/user_data.php";


?>

<!DOCTYPE html>
<html lang="az">

<head>
	<?php require_once './inc/head.php'; ?>
</head>

<body>


	<div class="main-wrapper home-nine">

		<!-- Header -->
		<?php require_once './inc/header.php'; ?>
		<!-- /Header -->



		<!-- Profile Content -->
		<div class="dashboard-content">
			<div class="container">
				<div style="margin-top: 70px">
					<?php include "./inc/dashboard_menus.php"; ?>
				</div>
				<div class="profile-content">
					<div class="row dashboard-info chat-window">
						<div class="col-lg-4">
							<div class="chat-cont-left">
								<form class="chat-search">
									<div class="form-set">
										<div class="group-img">
											<img src="assets/img/chatsearch.svg" alt="">
											<input type="text" class="form-control" placeholder="Söhbət axtar ...">
										</div>
									</div>
								</form>
								<div class="chat-users-list" id="conversationList">
									<div class="chat-scroll">

										<?php
										$stmt = $pdo->prepare("
											SELECT 
												c.id,
												c.listing_id,
												c.user1_id,
												c.user2_id,
												c.last_message_at,
												l.title,
												u.full_name,
												 COALESCE(m.logo, 'assets/img/user.png') AS logo,
												(
													SELECT message 
													FROM messages 
													WHERE conversation_id = c.id 
													ORDER BY created_at DESC 
													LIMIT 1
												) as last_message,
												(
													SELECT COUNT(*) 
													FROM messages 
													WHERE conversation_id = c.id 
													AND is_read = 0 
													AND customer_id != ?
												) as unread_count
											FROM conversations c
											JOIN listings l ON l.id = c.listing_id
											JOIN customers u ON u.id = 
												CASE 
													WHEN c.user1_id = ? THEN c.user2_id
													ELSE c.user1_id
												END
											LEFT JOIN markets m ON m.customer_id = u.id
											WHERE c.user1_id = ? OR c.user2_id = ?
											ORDER BY c.last_message_at DESC
										");

										$stmt->execute([
											$_SESSION['customer_id'],
											$_SESSION['customer_id'],
											$_SESSION['customer_id'],
											$_SESSION['customer_id']
										]);

										$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
										?>

										<?php foreach ($conversations as $conv): ?>

											<a href="javascript:void(0);" class="media d-flex conversation-item"
												data-id="<?= $conv['id']; ?>">
												<div class="media-img-wrap flex-shrink-0">
													<div class="avatar">
														<img src="assets/img/market-logo/<?= $conv['logo']; ?>"
															alt="User Image" class="avatar-img rounded-circle">
													</div>
												</div>
												<div class="media-body flex-grow-1">
													<div>
														<div class="user-name"><?= htmlspecialchars($conv['full_name']); ?>
														</div>
														<div class="user-last-chat">
															<?= htmlspecialchars($conv['last_message'] ?? 'Mesaj yoxdur'); ?>
														</div>
													</div>
													<div>
														<div class="last-chat-time block">
															<?= $conv['last_message_at']
																? date('H:i', strtotime($conv['last_message_at']))
																: '' ?>
														</div>
														<?php if ($conv['unread_count'] > 0): ?>
															<div class="badge badge-success rounded-pill">
																<?= $conv['unread_count']; ?>
															</div>
														<?php endif; ?>
													</div>
												</div>
											</a>

										<?php endforeach; ?>

									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-8">
							<div class="chat-cont-right">
								<div class="chat-header">
									<a id="back_user_list" href="javascript:void(0)" class="back-user-list">
										<i class="fa-solid fa-chevron-left"></i>
									</a>
									<div class="media d-flex align-items-center">
										<div class="media-img-wrap flex-shrink-0">
											<div class="avatar">
												<img src="assets/img/market-logo/user.png" alt="User Image"
													class="avatar-img rounded-circle">
											</div>
										</div>
										<div class="media-body flex-grow-1">
											<div class="user-name"></div>
											<a class="product-name" target="_blank"></a>
										</div>

									</div>
								</div>
								<div class="chat-body">
									<div class="chat-scroll">

										<ul class="list-unstyled">

										</ul>
									</div>
								</div>
								<div class="chat-footer">
									<div class="input-group">
										<div class="attach-btn">
											<input type="text" class="input-msg-send form-control"
												placeholder="Mesaj...">

										</div>
										<button type="button" class="btn msg-send-btn"><i
												class="fas fa-paper-plane"></i></button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Profile Content -->

		<?php include __DIR__ . '/inc/alert_modal.php'; ?>

		<!-- Footer -->
		<?php require_once __DIR__ . '/inc/footer.php'; ?>
		<!-- /Footer -->

	</div>

	<!-- scrollToTop start -->
	<div class="progress-wrap active-progress">
		<svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
			<path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
				style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919px, 307.919px; stroke-dashoffset: 228.265px;">
			</path>
		</svg>
	</div>
	<!-- scrollToTop end -->



	<!-- jQuery -->
	<script src="assets/js/jquery-3.7.1.min.js"></script>

	<!-- Bootstrap Core JS -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>

	<!-- Select2 JS -->
	<script src="assets/plugins/select2/js/select2.min.js"></script>

	<!-- Aos -->
	<script src="assets/plugins/aos/aos.js"></script>

	<!-- Top JS -->
	<script src="assets/js/backToTop.js"></script>

	<!-- Fearther JS -->
	<script src="assets/js/feather.min.js"></script>

	<!-- Custom JS -->
	<script src="assets/js/script.js"></script>

	<script src="<?= $base_url; ?>/js/alert-modal.js"></script>

	<?php require_once __DIR__ . "/api/_csrf.php"; ?>



	<script>
		document.addEventListener("DOMContentLoaded", () => {

			const btn = document.getElementById("logoutBtn");
			if (!btn) return;

			btn.addEventListener("click", async (e) => {
				e.preventDefault();

				try {
					const res = await fetch("<?= $base_url; ?>/api/auth/logout.php", {
						method: "POST",
						headers: { "X-Requested-With": "XMLHttpRequest" }
					});

					const data = await res.json();

					if (data.ok) {
						window.location.href = "./login";
					}

				} catch (err) {
					showError("Logout zamanı xəta baş verdi.");
				}
			});

		});
	</script>


	<script>
		document.querySelectorAll(".conversation-item").forEach(item => {
			item.addEventListener("click", function () {

				item.classList.add("active");

				let conversationId = this.dataset.id;

				window.currentConversation = conversationId; // 🔥 BU YOX İDİ

				loadMessages(conversationId); // ilk yükləmə

			});
		});

		function scrollToBottom() {
			const box = document.querySelector(".messages-box");
			box.scrollTop = box.scrollHeight;
		}

		function loadMessages(conversationId) {

			fetch("<?= $base_url; ?>/api/messages/get.php?conversation_id=" + conversationId)
				.then(res => res.json())
				.then(data => {

					if (data.status !== "success") {
						console.log("API ERROR:", data);
						return;
					}

					document.querySelector(".chat-header .user-name").innerText = data.other_user;
					document.querySelector(".chat-header .avatar-img").src = 'assets/img/market-logo/' + data.logo;
					document.querySelector(".chat-header .product-name").innerHTML = '<small><i class="fas fa-tag text-primary"></i> ' + data.product_name + '</small>';
					document.querySelector(".chat-header .product-name").href = 'listing/' + data.product_slug + '/' + data.listing_id;

					let chatBody = document.querySelector(".chat-body ul");
					chatBody.innerHTML = "";

					let currentUser = <?= $_SESSION['customer_id']; ?>;


					data.messages.forEach(msg => {



						let li = `
							<li class="media d-flex ">
								<div class="media-body flex-grow-1">
									<div class="msg-box" style="${msg.is_mine ? 'float: right;' : 'float: left'}" >
										<div style="${msg.is_mine ? 'border-radius: 15px 15px 15px 0 !important; background: #c10037;' : 'border-radius: 15px 15px 0 15px !important; background: #5e00c1;'}">
											<p style="color: #fff;">${msg.message}</p>
											<ul class="chat-msg-info">
												<li>
													<div class="chat-time" style="text-align: right;">
														<span style="color: #cecece; font-size: 11px">${msg.time}</span>
													</div>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</li>
						`;

						chatBody.innerHTML += li;
					});

					scrollToBottom();

				})
				.catch(err => {
					console.log("FETCH ERROR:", err);
				});
		}

		setInterval(() => {

			if (!window.currentConversation) return;

			loadMessages(window.currentConversation);

		}, 60000);
	</script>

	<script>
		document.querySelector(".msg-send-btn").addEventListener("click", function () {

			let input = document.querySelector(".input-msg-send");
			let message = input.value.trim();

			if (!message || !window.currentConversation) return;

			input.addEventListener("keypress", function (e) {
				if (e.key === "Enter") {
					e.preventDefault();
					document.querySelector(".msg-send-btn").click();
				}
			});



			let formData = new FormData();
			formData.append("message", message);
			formData.append("conversation_id", window.currentConversation);
			formData.append("csrf_token", "<?= csrf_token(); ?>");

			fetch("<?= $base_url; ?>/api/messages/send.php", {
				method: "POST",
				body: formData
			})
				.then(res => res.json())
				.then(data => {

					if (data.status === "success") {

						input.value = "";

						// reload messages
						document.querySelector(`[data-id="${window.currentConversation}"]`).click();

					} else {
						showError(data.message);
					}

				});

		});
	</script>
</body>


</html>