<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="HemoLink - Blood Bank Management System. Manage blood donations, inventory, and hospital requests efficiently.">
    <title><?php echo $pageTitle ?? 'HemoLink'; ?> | HemoLink BBMS</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome 6.4.0 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'hemo-red': '#C41E3A',
                        'hemo-deep-red': '#8B0000',
                        'hemo-light-red': '#FFE4E1',
                        'hemo-navy': '#1E293B',
                        'hemo-charcoal': '#334155',
                        'hemo-gold': '#D4AF37',
                        'hemo-amber': '#F59E0B',
                        'hemo-success': '#059669',
                        'hemo-warning': '#DC2626',
                        'hemo-info': '#3B82F6',
                        'hemo-off-white': '#FAFAFA',
                        'hemo-border': '#E2E8F0',
                        'hemo-gray': '#64748B',
                        'hemo-light-gray': '#F1F5F9',
                        'hemo-hover': '#F8FAFC',
                    },
                    fontFamily: {
                        'display': ['Playfair Display', 'serif'],
                        'interface': ['Inter', 'sans-serif'],
                    },
                    spacing: {
                        'sidebar': '260px',
                    },
                    boxShadow: {
                        'card': '0 1px 3px rgba(0,0,0,0.1)',
                        'card-hover': '0 10px 25px rgba(0,0,0,0.1)',
                        'btn-primary': '0 4px 6px -1px rgba(196, 30, 58, 0.3)',
                        'btn-hover': '0 8px 15px -3px rgba(196, 30, 58, 0.4)',
                        'modal': '0 20px 25px -5px rgba(0,0,0,0.1)',
                    }
                }
            }
        }
    </script>

    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #C41E3A; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #8B0000; }

        /* Transition defaults */
        .transition-default { transition: all 0.3s ease; }
        .transition-fast { transition: all 0.2s ease; }

        /* Sidebar active state */
        .sidebar-item.active {
            background: #FFE4E1;
            border-left: 3px solid #C41E3A;
            color: #C41E3A;
        }
        .sidebar-item:hover {
            background: #FFE4E1;
            color: #C41E3A;
        }

        /* Card left border accent */
        .card-accent-red { border-left: 4px solid #C41E3A; }
        .card-accent-amber { border-left: 4px solid #F59E0B; }
        .card-accent-deep-red { border-left: 4px solid #8B0000; }
        .card-accent-green { border-left: 4px solid #059669; }
        .card-accent-gold { border-left: 4px solid #D4AF37; }
        .card-accent-blue { border-left: 4px solid #3B82F6; }

        /* Gradient backgrounds */
        .gradient-red { background: linear-gradient(135deg, #C41E3A 0%, #8B0000 100%); }
        .gradient-red-radial { 
            background: linear-gradient(135deg, #C41E3A 0%, #8B0000 100%);
            position: relative;
        }
        .gradient-red-radial::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 50%, rgba(255,255,255,0.1) 0%, transparent 70%);
        }

        /* Glassmorphism card */
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
        }

        /* Skeleton loading */
        .skeleton {
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        /* Pulse animation for notifications */
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }

        /* Input focus state */
        input:focus, select:focus, textarea:focus {
            border-color: #C41E3A !important;
            box-shadow: 0 0 0 3px rgba(196, 30, 58, 0.1) !important;
            outline: none;
        }

        /* Button press micro-interaction */
        .btn-press:active {
            transform: scale(0.97);
            transition: transform 0.15s;
        }

        /* Stat card hover */
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        /* Table row hover */
        .table-row:hover {
            background-color: #F8FAFC;
        }

        /* Blood type card */
        .blood-card:hover {
            border-color: #C41E3A;
            background: white;
        }
    </style>
</head>
<body class="font-interface bg-hemo-off-white text-hemo-navy antialiased">
