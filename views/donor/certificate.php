<?php
/**
 * Official Donor Recognition Certificate View
 * High-definition printable certificate with landscape layout
 */
$pageTitle = 'Donation Certificate';
$donor = $data['donor'] ?? null;
$stats = $data['stats'] ?? ['total_donations' => 0, 'last_donation_date' => null, 'total_volume_ml' => 0];
$specificDonation = $data['donation'] ?? null;

$donorName = sanitize(($donor['first_name'] ?? 'Valued') . ' ' . ($donor['last_name'] ?? 'Donor'));
$bloodType = sanitize($donor['blood_type'] ?? 'Verified Donor');
$certId = 'HL-CERT-' . str_pad($donor['user_id'] ?? 1, 5, '0', STR_PAD_LEFT) . ($specificDonation ? '-' . $specificDonation['donation_id'] : '');
$issueDate = $specificDonation ? date('F j, Y', strtotime($specificDonation['donation_date'])) : ($stats['last_donation_date'] ? date('F j, Y', strtotime($stats['last_donation_date'])) : date('F j, Y'));
$totalDonations = (int)($stats['total_donations'] ?? 1);
$volumeMl = $specificDonation ? (int)$specificDonation['volume_ml'] : (int)($stats['total_volume_ml'] ?? 450);

// If standalone print mode or preview
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donor Certificate - <?php echo $donorName; ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Inter:wght@300;400;500;600;700&family=Alex+Brush&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 0;
            }
            body {
                background: white !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            .cert-container {
                box-shadow: none !important;
                border: none !important;
                width: 100vw !important;
                height: 100vh !important;
                max-width: none !important;
                border-radius: 0 !important;
            }
        }
        .font-cinzel { font-family: 'Cinzel', serif; }
        .font-signature { font-family: 'Alex Brush', cursive; }
    </style>
</head>
<body class="bg-slate-900 min-h-screen py-8 px-4 flex flex-col items-center justify-center font-sans antialiased">

    <!-- Action Bar (Hidden in Print) -->
    <div class="no-print w-full max-w-4xl mb-6 flex flex-wrap items-center justify-between gap-4 bg-slate-800/80 backdrop-blur-md p-4 rounded-2xl border border-slate-700 shadow-xl text-white">
        <div class="flex items-center gap-3">
            <a href="<?php echo BASE_URL; ?>/index.php?page=donor_dashboard" class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-sm font-semibold transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
            <span class="text-xs text-slate-400 font-mono"><?php echo $certId; ?></span>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-sm shadow-lg shadow-red-600/30 transition-all flex items-center gap-2">
                <i class="fas fa-print"></i> Print / Save as PDF
            </button>
        </div>
    </div>

    <!-- Certificate Wrapper -->
    <div class="cert-container w-full max-w-4xl aspect-[1.414/1] bg-[#FCFBF7] text-slate-900 rounded-3xl shadow-2xl p-8 sm:p-12 relative overflow-hidden border-8 border-double border-[#C5A059] flex flex-col justify-between">
        
        <!-- Background Watermark -->
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.03]">
            <i class="fas fa-droplet text-[380px] text-red-900"></i>
        </div>

        <!-- Corner Ornaments -->
        <div class="absolute top-4 left-4 w-12 h-12 border-t-2 border-l-2 border-[#C5A059]"></div>
        <div class="absolute top-4 right-4 w-12 h-12 border-t-2 border-r-2 border-[#C5A059]"></div>
        <div class="absolute bottom-4 left-4 w-12 h-12 border-b-2 border-l-2 border-[#C5A059]"></div>
        <div class="absolute bottom-4 right-4 w-12 h-12 border-b-2 border-r-2 border-[#C5A059]"></div>

        <!-- Header -->
        <div class="text-center relative z-10">
            <div class="inline-flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center text-white text-sm shadow">
                    <i class="fas fa-droplet"></i>
                </div>
                <span class="font-cinzel text-sm font-bold tracking-[0.25em] text-red-800 uppercase">HemoLink National Blood Service</span>
            </div>
            <h1 class="font-cinzel text-3xl sm:text-4xl font-extrabold text-[#1A2634] tracking-wider uppercase mt-1">
                Certificate of Appreciation
            </h1>
            <p class="text-xs font-semibold text-[#C5A059] uppercase tracking-[0.3em] mt-1">
                For Invaluable Voluntary Blood Donation
            </p>
        </div>

        <!-- Body Content -->
        <div class="text-center my-auto py-4 relative z-10">
            <p class="text-xs text-slate-500 uppercase tracking-widest font-medium">This is proudly presented to</p>
            
            <div class="my-3">
                <h2 class="font-cinzel text-2xl sm:text-3xl font-bold text-red-900 tracking-wide border-b-2 border-[#C5A059]/40 pb-2 inline-block px-8">
                    <?php echo $donorName; ?>
                </h2>
            </div>

            <p class="text-xs sm:text-sm text-slate-700 max-w-xl mx-auto leading-relaxed mt-2 font-serif">
                In sincere recognition of your selfless generosity and noble humanitarian act of donating blood. 
                Your contribution of <strong class="text-slate-900"><?php echo number_format($volumeMl); ?> ml</strong> (Blood Group <strong class="text-red-700"><?php echo $bloodType; ?></strong>) provides hope, saves precious lives, and strengthens our community.
            </p>

            <div class="mt-4 inline-flex items-center gap-6 px-6 py-2 rounded-full bg-[#F4EFE6] border border-[#C5A059]/30 text-xs">
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-semibold">Total Donations</span>
                    <strong class="text-slate-900 font-bold"><?php echo $totalDonations; ?> Lifetime</strong>
                </div>
                <div class="h-6 w-px bg-[#C5A059]/40"></div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-semibold">Blood Group</span>
                    <strong class="text-red-700 font-bold"><?php echo $bloodType; ?></strong>
                </div>
                <div class="h-6 w-px bg-[#C5A059]/40"></div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-semibold">Date of Recognition</span>
                    <strong class="text-slate-900 font-bold"><?php echo $issueDate; ?></strong>
                </div>
            </div>
        </div>

        <!-- Footer / Signatures -->
        <div class="grid grid-cols-3 items-end pt-4 border-t border-[#C5A059]/30 relative z-10 text-center">
            <!-- Left Signature -->
            <div>
                <div class="font-signature text-2xl text-slate-800 -mb-1">Dr. S. K. Mwangi</div>
                <div class="w-36 h-px bg-slate-400 mx-auto mb-1"></div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-800">Medical Director</p>
                <p class="text-[8px] text-slate-500">Transfusion Medicine</p>
            </div>

            <!-- Center Seal -->
            <div class="flex flex-col items-center justify-center">
                <div class="w-16 h-16 rounded-full border-2 border-dashed border-[#C5A059] flex items-center justify-center p-1 shadow-inner bg-amber-50/50">
                    <div class="w-full h-full rounded-full bg-[#C5A059]/20 flex flex-col items-center justify-center text-[7px] font-cinzel font-bold text-amber-900 text-center uppercase leading-tight">
                        <i class="fas fa-certificate text-amber-700 text-sm mb-0.5"></i>
                        Official Seal
                    </div>
                </div>
                <span class="font-mono text-[8px] text-slate-400 mt-1"><?php echo $certId; ?></span>
            </div>

            <!-- Right Signature -->
            <div>
                <div class="font-signature text-2xl text-slate-800 -mb-1">Grace A. Ochieng</div>
                <div class="w-36 h-px bg-slate-400 mx-auto mb-1"></div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-800">Head of Blood Bank</p>
                <p class="text-[8px] text-slate-500">Quality & Safety Standards</p>
            </div>
        </div>

    </div>

</body>
</html>
