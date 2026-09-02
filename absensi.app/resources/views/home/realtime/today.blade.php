@extends('layouts.home.display')
@section('title', 'Realtime | Absensi UII Dalwa')

@push('css')
    <style>
        /* ========================================
           FULLSCREEN DISPLAY STYLE - AIRPORT/KAI
           ======================================== */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #0f0f23 100%);
        }

        body {
            min-height: 100vh;
            font-family: 'Segoe UI', 'Roboto', 'Arial', sans-serif;
        }

        .main {
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Section realtime fullscreen */
        .realtime-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 0;
            margin: 0;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #0f0f23 100%);
        }

        /* Header area */
        .display-header {
            background: linear-gradient(90deg, #1a1a2e 0%, #16213e 50%, #1a1a2e 100%);
            padding: 15px 30px;
            border-bottom: 2px solid #ffc107;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .display-header h1 {
            color: #ffc107;
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 0;
        }

        .display-header .clock {
            color: #00ff88;
            font-size: 1.3rem;
            font-weight: 600;
            font-family: 'Consolas', 'Monaco', monospace;
        }

        .display-header .date-info {
            color: #fff;
            font-size: 0.95rem;
            opacity: 0.9;
        }

        /* Table container */
        .table-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px 30px;
            overflow: hidden;
        }

        /* DataTables wrapper */
        .dataTables_wrapper {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        /* Main table styling */
        #realtimeTable {
            width: 100% !important;
            font-size: 0.85rem;
            border-collapse: separate;
            border-spacing: 0 4px;
        }

        #realtimeTable thead th {
            background: linear-gradient(180deg, #1e3a5f 0%, #0f2847 100%);
            color: #ffc107;
            vertical-align: middle;
            padding: 12px 15px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            border: none;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        #realtimeTable thead th:first-child {
            border-radius: 4px 0 0 4px;
        }

        #realtimeTable thead th:last-child {
            border-radius: 0 4px 4px 0;
        }

        #realtimeTable tbody {
            perspective: 1000px;
        }

        #realtimeTable tbody tr {
            background: rgba(255, 255, 255, 0.03);
            transition: all 0.3s ease;
            transform-style: preserve-3d;
        }

        #realtimeTable tbody tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.06);
        }

        #realtimeTable tbody tr:hover {
            background: rgba(255, 193, 7, 0.1);
            transform: scale(1.005);
        }

        #realtimeTable tbody td {
            color: #e8e8e8;
            padding: 10px 15px;
            font-size: 0.82rem;
            border: none;
            vertical-align: middle;
        }

        /* Flip animation for table cells */
        .flip-cell {
            display: inline-block;
            transform-style: preserve-3d;
            animation: none;
        }

        .flip-in {
            animation: flipInX 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        @keyframes flipInX {
            0% {
                transform: perspective(400px) rotateX(90deg);
                opacity: 0;
            }

            40% {
                transform: perspective(400px) rotateX(-10deg);
            }

            70% {
                transform: perspective(400px) rotateX(10deg);
            }

            100% {
                transform: perspective(400px) rotateX(0deg);
                opacity: 1;
            }
        }

        /* Staggered flip animation for rows */
        .flip-row {
            animation: flipInRow 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            animation-fill-mode: both;
        }

        @keyframes flipInRow {
            0% {
                transform: perspective(1000px) rotateX(-90deg) translateY(-20px);
                opacity: 0;
            }

            60% {
                transform: perspective(1000px) rotateX(15deg);
            }

            100% {
                transform: perspective(1000px) rotateX(0deg) translateY(0);
                opacity: 1;
            }
        }

        /* Hide footer if visible */
        #realtimeTable tfoot {
            display: none;
        }

        /* Custom scrollbar */
        .table-responsive::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: rgba(255, 193, 7, 0.3);
            border-radius: 3px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 193, 7, 0.5);
        }

        /* Status bar at bottom */
        .status-bar {
            background: linear-gradient(90deg, #1a1a2e 0%, #16213e 50%, #1a1a2e 100%);
            padding: 8px 30px;
            border-top: 1px solid rgba(255, 193, 7, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-bar .page-info {
            color: #888;
            font-size: 0.75rem;
        }

        .status-bar .update-info {
            color: #00ff88;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-bar .update-info::before {
            content: '';
            width: 8px;
            height: 8px;
            background: #00ff88;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        /* ========================================
               GALLERY MARQUEE STYLES
               ======================================== */
        .gallery-marquee-container {
            background: linear-gradient(90deg, #0d1117 0%, #161b22 50%, #0d1117 100%);
            border-top: 1px solid rgba(255, 193, 7, 0.2);
            padding: 10px 0;
            overflow: hidden;
            position: relative;
        }

        .gallery-marquee-container::before,
        .gallery-marquee-container::after {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 80px;
            z-index: 2;
            pointer-events: none;
        }

        .gallery-marquee-container::before {
            left: 0;
            background: linear-gradient(to right, #0d1117, transparent);
        }

        .gallery-marquee-container::after {
            right: 0;
            background: linear-gradient(to left, #0d1117, transparent);
        }

        .gallery-marquee {
            display: flex;
            gap: 15px;
            animation: marqueeScroll 60s linear infinite;
            width: max-content;
        }

        .gallery-marquee:hover {
            animation-play-state: paused;
        }

        @keyframes marqueeScroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .gallery-item {
            flex-shrink: 0;
            width: 120px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .gallery-item:hover {
            transform: scale(1.1);
            border-color: #ffc107;
            box-shadow: 0 6px 25px rgba(255, 193, 7, 0.3);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .no-gallery-message {
            color: #666;
            font-size: 0.8rem;
            text-align: center;
            padding: 20px;
            font-style: italic;
        }

        /* Responsive adjustments */
        @media only screen and (max-width: 768px) {
            .display-header {
                padding: 10px 15px;
                flex-direction: column;
                gap: 5px;
            }

            .display-header h1 {
                font-size: 1rem;
            }

            .table-container {
                padding: 10px 15px;
            }

            #realtimeTable {
                font-size: 0.7rem;
            }

            #realtimeTable thead th,
            #realtimeTable tbody td {
                padding: 8px 10px;
                font-size: 0.7rem;
            }

            .status-bar {
                padding: 6px 15px;
            }

            .gallery-item {
                width: 100px;
                height: 65px;
            }

            .gallery-marquee {
                gap: 10px;
            }
        }

        @media only screen and (max-width: 480px) {

            #realtimeTable thead th,
            #realtimeTable tbody td {
                padding: 6px 8px;
                font-size: 0.65rem;
            }

            .gallery-item {
                width: 80px;
                height: 55px;
            }
        }
    </style>
@endpush

@section('content')
    <section class="realtime-wrapper">
        <!-- Header -->
        <div class="display-header">
            <h1>📋 Data Absensi Hari Ini</h1>
            <div style="text-align: right;">
                <div class="clock" id="liveClock">--:--:--</div>
                <div class="date-info" id="liveDate">Loading...</div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-container">
            <div class="table-responsive" style="flex: 1; overflow: hidden;">
                <table id="realtimeTable" class="table">
                    <thead>
                        <tr>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Jam</th>
                            <th class="text-center">Nama</th>
                            <th class="text-center">Departemen</th>
                            <th class="text-center">Lokasi Absen</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <!-- Status Bar -->
        <div class="status-bar">
            <div class="page-info" id="pageInfo">Halaman 1 dari 1</div>
            <div class="update-info">Auto-update setiap 8 detik</div>
        </div>

        <!-- Gallery Marquee -->
        <div class="gallery-marquee-container">
            <div class="gallery-marquee" id="galleryMarquee">
                <div class="no-gallery-message">Memuat galeri foto...</div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        let realtimeTable;
        let isTransitioning = false;

        // Live clock
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

            document.getElementById('liveClock').textContent = timeStr;
            document.getElementById('liveDate').textContent = dateStr;
        }

        // Calculate page length based on screen height
        function setPageLengthByHeight() {
            if (!realtimeTable) return;

            const $table = $('#realtimeTable');
            const $firstRow = $table.find('tbody tr:first');

            if (!$firstRow.length) return;

            const rowH = $firstRow.outerHeight(true) || 45;
            const containerH = $('.table-container').height();
            const theadH = $table.find('thead').outerHeight() || 50;
            const available = containerH - theadH - 20;
            const pageLen = Math.max(5, Math.floor(available / rowH));

            realtimeTable.page.len(pageLen).draw(false);
        }

        // Apply flip animation to rows
        function applyFlipAnimation() {
            const rows = $('#realtimeTable tbody tr');
            rows.each(function (index) {
                const $row = $(this);
                $row.removeClass('flip-row');

                // Stagger the animation
                setTimeout(() => {
                    $row.addClass('flip-row');
                }, index * 80); // 80ms delay between each row
            });
        }

        // Update page info
        function updatePageInfo() {
            if (!realtimeTable) return;

            const info = realtimeTable.page.info();
            if (info) {
                const pageText = `Halaman ${info.page + 1} dari ${info.pages} • Total ${info.recordsTotal} data`;
                document.getElementById('pageInfo').textContent = pageText;
            }
        }

        // Load gallery images for marquee
        function loadGalleryMarquee() {
            $.ajax({
                url: "{{ route('api.gallery.latest') }}",
                method: "GET",
                data: { limit: 15 },
                success: function (images) {
                    const $marquee = $('#galleryMarquee');
                    $marquee.empty();

                    if (!images || images.length === 0) {
                        $marquee.html('<div class="no-gallery-message">Belum ada foto galeri</div>');
                        return;
                    }

                    // Create image elements (duplicate for seamless loop)
                    let html = '';

                    // First set of images
                    images.forEach(img => {
                        html += `<div class="gallery-item">
                                <img src="${img.url}" alt="${img.caption || 'Gallery'}" loading="lazy">
                            </div>`;
                    });

                    // Duplicate for seamless infinite scroll
                    images.forEach(img => {
                        html += `<div class="gallery-item">
                                <img src="${img.url}" alt="${img.caption || 'Gallery'}" loading="lazy">
                            </div>`;
                    });

                    $marquee.html(html);

                    // Adjust animation duration based on number of images
                    const duration = Math.max(30, images.length * 4); // ~4s per image
                    $marquee.css('animation-duration', duration + 's');
                },
                error: function () {
                    $('#galleryMarquee').html('<div class="no-gallery-message">Gagal memuat galeri</div>');
                }
            });
        }

        $(function () {
            // Start clock
            updateClock();
            setInterval(updateClock, 1000);

            // Load gallery marquee
            loadGalleryMarquee();
            // Refresh gallery every 5 minutes
            setInterval(loadGalleryMarquee, 300000);

            // Initialize DataTable
            realtimeTable = $("#realtimeTable").DataTable({
                autoWidth: true,
                processing: false,
                serverSide: true,
                dom: 't',
                paging: true,
                searching: false,
                lengthChange: false,
                info: false,

                ajax: {
                    url: "{{ route('realtime.data.today') }}",
                    method: "GET",
                },

                columns: [
                    { class: "text-center", data: "tgl_absen" },
                    { class: "text-center", data: "pagi" },
                    { class: "text-center", data: "user_name" },
                    { class: "text-center", data: "departemen_nama" },
                    { class: "text-center", data: "device_name" },
                ],
                order: [
                    [0, "desc"],
                    [1, "desc"]
                ],

                drawCallback: function () {
                    applyFlipAnimation();
                    updatePageInfo();
                },

                initComplete: function () {
                    setPageLengthByHeight();

                    let resizeTimer;
                    $(window).on('resize', function () {
                        clearTimeout(resizeTimer);
                        resizeTimer = setTimeout(function () {
                            realtimeTable.ajax.reload(function () {
                                setPageLengthByHeight();
                            }, false);
                        }, 300);
                    });
                }
            });

            // Auto reload + auto slide with smooth flip transition
            setInterval(function () {
                if (!realtimeTable || isTransitioning) return;

                const info = realtimeTable.page.info();

                // No pagination - just reload
                if (!info || info.pages <= 1) {
                    isTransitioning = true;
                    $('#realtimeTable tbody').css('opacity', '0.3');

                    realtimeTable.ajax.reload(function () {
                        $('#realtimeTable tbody').css('opacity', '1');
                        isTransitioning = false;
                    }, false);
                    return;
                }

                // Has pagination - use flip transition
                isTransitioning = true;
                const $tbody = $('#realtimeTable tbody');
                const $rows = $tbody.find('tr');

                // Animate rows out (flip out)
                $rows.each(function (index) {
                    const $row = $(this);
                    setTimeout(() => {
                        $row.css({
                            'transform': 'perspective(1000px) rotateX(90deg)',
                            'opacity': '0',
                            'transition': 'all 0.4s cubic-bezier(0.55, 0.085, 0.68, 0.53)'
                        });
                    }, index * 50);
                });

                // After flip out animation completes
                setTimeout(function () {
                    realtimeTable.ajax.reload(function () {
                        setPageLengthByHeight();

                        const infoAfter = realtimeTable.page.info();
                        if (infoAfter && infoAfter.pages > 1) {
                            const nextPage = (infoAfter.page + 1) % infoAfter.pages;
                            realtimeTable.page(nextPage).draw(false);
                        }

                        // Reset transform for new rows
                        $('#realtimeTable tbody tr').css({
                            'transform': '',
                            'opacity': '',
                            'transition': ''
                        });

                        isTransitioning = false;
                    }, false);
                }, $rows.length * 50 + 500); // Wait for flip out to complete

            }, 8000); // 8 seconds interval for smoother experience
        });
    </script>
@endpush