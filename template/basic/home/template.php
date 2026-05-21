<div class="container" id="page-welcome">

    <!-- Hero -->
    <div class="hc-hero">
        <div class="hc-hero-glow"></div>
        <div class="hc-hero-inner">
            <img src="https://yllumi.github.io/heroic/assets/logo-text.png" class="hc-logo-img" alt="Heroic">
            <p class="hc-tagline">PHP · Alpine.js · Pinecone Router</p>
            <span class="hc-badge">v<?= $version ?? '1.0.0' ?> &nbsp;·&nbsp; PHP <?= $php_version ?? PHP_VERSION ?></span>
        </div>
    </div>

    <!-- Stack -->
    <div class="hc-section">
        <p class="hc-section-label">Built on</p>
        <div class="hc-stack-list">
            <?php foreach ($stack ?? [] as $item): ?>
                <div class="hc-stack-card">
                    <div class="hc-stack-icon" style="background:<?= htmlspecialchars($item['color']) ?>22;color:<?= htmlspecialchars($item['color']) ?>">
                        <i class="bi <?= htmlspecialchars($item['icon']) ?>"></i>
                    </div>
                    <div>
                        <div class="hc-stack-name"><?= htmlspecialchars($item['name']) ?></div>
                        <div class="hc-stack-desc"><?= htmlspecialchars($item['desc']) ?></div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>

    <!-- Features -->
    <div class="hc-section">
        <p class="hc-section-label">What's included</p>
        <div class="hc-feature-grid">
            <?php foreach ($features ?? [] as $f): ?>
                <div class="hc-feature-card">
                    <i class="bi <?= htmlspecialchars($f['icon']) ?> hc-feature-icon"></i>
                    <div class="hc-feature-title"><?= htmlspecialchars($f['title']) ?></div>
                    <div class="hc-feature-desc"><?= htmlspecialchars($f['desc']) ?></div>
                </div>
            <?php endforeach ?>
        </div>
    </div>

    <!-- Quick Start -->
    <div class="hc-section">
        <p class="hc-section-label">Quick start</p>
        <div class="hc-code-block">
            <span class="hc-code-comment"># Add a new page</span><br>
            app/pages/<strong>your-page</strong>/PageController.php<br>
            app/pages/<strong>your-page</strong>/template.php
        </div>
        <div class="hc-code-block mt-2">
            <span class="hc-code-comment"># Register the route</span><br>
            #[FrontendRoute(route: '/<strong>your-page</strong>')]
        </div>
    </div>

    <!-- Footer -->
    <div class="hc-footer">
        Edit <code>app/pages/home/template.php</code> to get started.
    </div>

</div>

<style>
    #page-welcome {
        min-height: 100vh;
        background: #fff;
        color: #1e1e2e;
        font-family: 'Poppins', sans-serif;
        padding-bottom: 48px;
    }

    .hc-hero {
        position: relative;
        overflow: hidden;
        padding: 64px 24px 48px;
        text-align: center;
    }

    .hc-hero-glow {
        position: absolute;
        top: -60px;
        left: 50%;
        transform: translateX(-50%);
        width: 480px;
        height: 480px;
        background: radial-gradient(circle, #fb923c30 0%, transparent 70%);
        pointer-events: none;
    }

    .hc-hero-inner {
        position: relative;
    }

    .hc-logo-img {
        height: 68px;
        width: auto;
        margin-bottom: 20px;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }

    .hc-tagline {
        font-size: 17px;
        color: #78716c;
        margin: 0 0 18px;
        letter-spacing: 0.5px;
    }

    .hc-badge {
        display: inline-block;
        font-size: 14px;
        font-weight: 600;
        padding: 5px 14px;
        border-radius: 99px;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        color: #ea580c;
    }

    .hc-section {
        padding: 0 20px;
        margin-top: 36px;
    }

    .hc-section-label {
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #a8a29e;
        margin-bottom: 14px;
    }

    .hc-stack-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .hc-stack-card {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        background: #fff;
        border: 1px solid #e7e5e4;
        border-radius: 14px;
        padding: 14px 16px;
        box-shadow: 0 1px 4px #0000000a;
    }

    .hc-stack-icon {
        flex-shrink: 0;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .hc-stack-name {
        font-size: 16px;
        font-weight: 600;
        color: #1c1917;
        line-height: 1.3;
    }

    .hc-stack-desc {
        font-size: 14px;
        color: #78716c;
        line-height: 1.5;
        margin-top: 2px;
    }

    .hc-feature-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .hc-feature-card {
        background: #fff;
        border: 1px solid #e7e5e4;
        border-radius: 14px;
        padding: 16px;
        box-shadow: 0 1px 4px #0000000a;
    }

    .hc-feature-icon {
        font-size: 26px;
        color: #f97316;
        margin-bottom: 10px;
        display: block;
    }

    .hc-feature-title {
        font-size: 15px;
        font-weight: 700;
        color: #1c1917;
        margin-bottom: 6px;
    }

    .hc-feature-desc {
        font-size: 14px;
        color: #78716c;
        line-height: 1.6;
    }

    .hc-code-block {
        background: #fafaf9;
        border: 1px solid #e7e5e4;
        border-radius: 12px;
        padding: 16px 18px;
        font-family: 'Courier New', monospace;
        font-size: 14px;
        color: #44403c;
        line-height: 2.2;
    }

    .hc-code-block strong {
        color: #ea580c;
    }

    .hc-code-comment {
        color: #a8a29e;
    }

    .hc-footer {
        margin-top: 40px;
        text-align: center;
        font-size: 14px;
        color: #a8a29e;
        padding: 0 24px;
    }

    .hc-footer code {
        color: #ea580c;
        background: #fff7ed;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 14px;
    }
</style>