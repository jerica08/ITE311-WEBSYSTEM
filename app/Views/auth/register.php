<?php $session = session(); ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Register</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
	<div class="container py-5">
		<div class="row justify-content-center">
			<div class="col-md-6">
				<div class="card shadow-sm">
					<div class="card-body p-4">
						<h1 class="h4 mb-4">Create your account</h1>

						<?php if ($session->getFlashdata('success')): ?>
							<div class="alert alert-success"><?= esc($session->getFlashdata('success')) ?></div>
						<?php endif; ?>
						<?php if ($session->getFlashdata('error')): ?>
							<div class="alert alert-danger white-space-pre-line"><?= esc($session->getFlashdata('error')) ?></div>
						<?php endif; ?>

						<form method="post" action="<?= site_url('/register') ?>" novalidate>
							<?= csrf_field() ?>
							<div class="mb-3">
								<label class="form-label">Full name</label>
								<input type="text" name="name" class="form-control" value="<?= set_value('name') ?>" required>
							</div>
							<div class="mb-3">
								<label class="form-label">Email address</label>
								<input type="email" name="email" class="form-control" value="<?= set_value('email') ?>" required>
							</div>
							<div class="mb-3">
								<label class="form-label">Password</label>
								<input type="password" name="password" class="form-control" required>
							</div>
							<div class="mb-4">
								<label class="form-label">Confirm password</label>
								<input type="password" name="password_confirm" class="form-control" required>
							</div>
							<div class="d-grid gap-2">
								<button type="submit" class="btn btn-primary">Register</button>
								<a class="btn btn-outline-secondary" href="<?= site_url('/login') ?>">Back to login</a>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


