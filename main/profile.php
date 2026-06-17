<?php
session_start();
require '../register/dbconnect.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../register/login.php');
    exit();
}

$userId = (int) $_SESSION['id'];
$error = [];
$success = '';

$avatarColumnExists = false;
$checkColumn = $db->query("SHOW COLUMNS FROM members LIKE 'avatar'");
if ($checkColumn && $checkColumn->rowCount() > 0) {
    $avatarColumnExists = true;
}

$query = 'SELECT name, email, created_at';
if ($avatarColumnExists) {
    $query .= ', avatar';
}
$query .= ' FROM members WHERE id = ? LIMIT 1';

$stmt = $db->prepare($query);
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: ../register/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $newName = trim($_POST['name'] ?? '');
    $newEmail = trim($_POST['email'] ?? '');

    if ($newName === '') {
        $error['name'] = 'Please enter your name.';
    }

    if ($newEmail === '') {
        $error['email'] = 'Please enter your email.';
    } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $error['email'] = 'Please enter a valid email address.';
    } else {
        $checkStmt = $db->prepare('SELECT COUNT(*) FROM members WHERE email = ? AND id != ?');
        $checkStmt->execute([$newEmail, $userId]);
        if ((int) $checkStmt->fetchColumn() > 0) {
            $error['email'] = 'This email is already used by another account.';
        }
    }

    $newAvatarPath = $user['avatar'] ?? null;
    if (!empty($_FILES['avatar']['name'])) {
        if (!$avatarColumnExists) {
            $error['avatar'] = 'Avatar support is not enabled for this database yet.';
        } else {
            $avatarFile = $_FILES['avatar'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            if (!in_array($avatarFile['type'], $allowedTypes, true)) {
                $error['avatar'] = 'Only JPG, PNG, GIF, and WEBP images are allowed.';
            } elseif ($avatarFile['size'] > 2 * 1024 * 1024) {
                $error['avatar'] = 'Image size must be less than 2MB.';
            } elseif (!is_uploaded_file($avatarFile['tmp_name'])) {
                $error['avatar'] = 'Unable to upload image.';
            } else {
                $uploadDir = __DIR__ . '/../uploads';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext = strtolower(pathinfo($avatarFile['name'], PATHINFO_EXTENSION));
                $fileName = 'avatar_' . $userId . '_' . time() . '.' . $ext;
                $targetPath = $uploadDir . '/' . $fileName;
                $relativePath = 'uploads/' . $fileName;

                if (move_uploaded_file($avatarFile['tmp_name'], $targetPath)) {
                    if (!empty($user['avatar']) && file_exists(__DIR__ . '/../' . $user['avatar'])) {
                        unlink(__DIR__ . '/../' . $user['avatar']);
                    }
                    $newAvatarPath = $relativePath;
                } else {
                    $error['avatar'] = 'Failed to save the uploaded image.';
                }
            }
        }
    }

    if (empty($error)) {
        if ($avatarColumnExists) {
            $updateStmt = $db->prepare('UPDATE members SET name = ?, email = ?, avatar = ? WHERE id = ?');
            $updateStmt->execute([$newName, $newEmail, $newAvatarPath, $userId]);
        } else {
            $updateStmt = $db->prepare('UPDATE members SET name = ?, email = ? WHERE id = ?');
            $updateStmt->execute([$newName, $newEmail, $userId]);
        }

        $_SESSION['name'] = $newName;
        $_SESSION['email'] = $newEmail;

        $user['name'] = $newName;
        $user['email'] = $newEmail;
        if ($avatarColumnExists) {
            $user['avatar'] = $newAvatarPath;
        }

        $success = 'Profile updated successfully.';
    }
}

$name = htmlspecialchars($user['name'] ?? 'User');
$email = htmlspecialchars($user['email'] ?? '');
$createdAt = date('F j, Y', strtotime($user['created_at']));
$avatarPath = $user['avatar'] ?? '';

$parts = preg_split('/\s+/', trim($user['name'] ?? ''));
$initials = '';
if (!empty($parts)) {
    $initials = strtoupper(substr($parts[0], 0, 1));
    if (count($parts) > 1) {
        $initials .= strtoupper(substr($parts[1], 0, 1));
    }
}
if ($initials === '') {
    $initials = 'U';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Personal Finance Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body style="min-height: 100vh;">
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center gap-2" href="main.php">
                <i class="bi bi-wallet2 text-primary"></i>
                Personal Finance Tracker
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="main.php">
                            <i class="bi bi-house-door-fill"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="profile.php">
                            <i class="bi bi-person-circle"></i>
                            <?= htmlspecialchars($_SESSION['name'] ?? 'User') ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../register/logout.php">
                            <i class="bi bi-box-arrow-right"></i> Log out
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                            <?php if (!empty($avatarPath) && file_exists(__DIR__ . '/../' . $avatarPath)): ?>
                                <img src="../<?= htmlspecialchars($avatarPath) ?>" alt="Profile avatar" class="profile-avatar rounded-circle object-fit-cover">
                            <?php else: ?>
                                <div class="profile-avatar rounded-circle d-flex align-items-center justify-content-center">
                                    <?= htmlspecialchars($initials) ?>
                                </div>
                            <?php endif; ?>
                            <div class="text-center text-md-start">
                                <p class="text-muted mb-1">Profile</p>
                                <h2 class="fw-bold mb-1"><?= $name ?></h2>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-envelope me-1"></i>
                                    <?= $email ?>
                                </p>
                            </div>
                        </div>

                        <?php if ($success !== ''): ?>
                            <div class="alert alert-success mt-4 mb-0">
                                <?= htmlspecialchars($success) ?>
                            </div>
                        <?php endif; ?>

                        <div class="row mt-4 g-3">
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <p class="text-muted small mb-1">Member since</p>
                                    <h5 class="mb-0"><?= htmlspecialchars($createdAt) ?></h5>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <p class="text-muted small mb-1">Account</p>
                                    <h5 class="mb-0">Personal Finance Tracker</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <h4 class="mb-3">Edit Profile</h4>
                        <form action="profile.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="update_profile" value="1">

                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control <?= isset($error['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>">
                                <?php if (isset($error['name'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($error['name']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control <?= isset($error['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                                <?php if (isset($error['email'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($error['email']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="avatar" class="form-label">Profile Image</label>
                                <input type="file" class="form-control <?= isset($error['avatar']) ? 'is-invalid' : '' ?>" id="avatar" name="avatar" accept="image/*">
                                <?php if (isset($error['avatar'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($error['avatar']) ?></div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
