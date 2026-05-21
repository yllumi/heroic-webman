<!-- Alpinejs Routers -->
<div 
    id="subpage" 
    x-data="$heroic.page()">
    <div class="page-content">
        <div id="subpage" class="page page-subpage">
            <div class="appHeader">
                <div class="left">
                </div>
                <div class="pageTitle" x-text="data.page_title"></div>
                <div class="right">
                </div>
            </div>

            <!-- App Capsule -->
            <div id="appCapsule">

                <div class="section mt-2">
                    <h2 class="text-center" x-text="data.message"></h2>

                    <a href="/">Kembali ke home</a>
                </div>
            </div>
            <!-- * App Capsule -->
        </div>
    </div>
</div>
