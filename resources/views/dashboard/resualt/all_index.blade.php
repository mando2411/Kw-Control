@extends('layouts.dashboard.app')

@section('content')
    <style>
        .results-pro-page {
            --rp-font-main: "Changa", "Cairo", sans-serif;
            --rp-ink: #11263d;
            --rp-muted: #5a6f87;
            --rp-surface: #ffffff;
            --rp-border: #d8e5f2;
            --rp-primary: #006c67;
            --rp-primary-soft: #d9f3f1;
            --rp-accent: #d97706;
            --rp-top: #2f855a;
            --rp-fifth: #8b5cf6;
            --rp-shadow: 0 18px 44px rgba(17, 38, 61, 0.14);
            --rp-shadow-soft: 0 10px 24px rgba(17, 38, 61, 0.1);

            position: relative;
            min-height: 100%;
            font-family: var(--rp-font-main);
            color: var(--rp-ink);
            padding: 1rem 0 2rem;
            overflow: hidden;
        }

        .results-pro-page::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(circle at 8% 16%, rgba(0, 108, 103, 0.14), transparent 42%),
                radial-gradient(circle at 92% 12%, rgba(217, 119, 6, 0.14), transparent 36%),
                linear-gradient(180deg, #f8fbff 0%, #eef4ff 100%);
        }

        .results-shell {
            position: relative;
            z-index: 1;
        }

        .results-hero {
            border: 1px solid var(--rp-border);
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: var(--rp-shadow);
            padding: 1rem 1.1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .results-title {
            margin: 0;
            font-size: clamp(1.12rem, 2.3vw, 1.58rem);
            font-weight: 800;
            letter-spacing: 0.01em;
            color: #153655;
        }

        .results-subtitle {
            margin: 0.32rem 0 0;
            color: var(--rp-muted);
            font-size: 0.92rem;
            font-weight: 600;
        }

        .results-meta-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            border-radius: 999px;
            padding: 0.4rem 0.82rem;
            font-weight: 700;
            font-size: 0.82rem;
            color: #0e4f4b;
            background: var(--rp-primary-soft);
        }

        .results-contact {
            border-radius: 14px;
            background: linear-gradient(135deg, #fff8d8 0%, #ffefb0 100%);
            color: #7b5603;
            font-weight: 700;
            font-size: 0.86rem;
            padding: 0.55rem 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .results-open-details {
            border: 0;
            min-height: 46px;
            border-radius: 13px;
            padding: 0.42rem 1rem;
            font-size: 0.95rem;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, var(--rp-primary) 0%, #008b84 100%);
            box-shadow: 0 11px 23px rgba(0, 108, 103, 0.28);
            transition: transform 0.2s ease, filter 0.2s ease;
        }

        .results-open-details:hover {
            transform: translateY(-1px);
            filter: brightness(0.97);
            color: #fff;
        }

        .results-view-switch-btn {
            border: 1px solid #c6d7ea;
            min-height: 46px;
            border-radius: 13px;
            padding: 0.42rem 0.9rem;
            font-size: 0.88rem;
            font-weight: 800;
            color: #16406a;
            background: linear-gradient(135deg, #f7fbff 0%, #e9f2ff 100%);
            box-shadow: 0 8px 16px rgba(17, 38, 61, 0.1);
            transition: transform 0.2s ease, filter 0.2s ease;
        }

        .results-view-switch-btn:hover {
            transform: translateY(-1px);
            filter: brightness(0.98);
        }

        .results-cards-grid {
            margin-top: 1rem;
            row-gap: 0.72rem;
        }

        .result-card-col {
            will-change: transform;
            width: 100%;
        }

        .results-cards-grid[data-view-mode="long"] .result-card-col {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .results-cards-grid[data-view-mode="grid"] .result-card-col {
            flex: 0 0 50%;
            max-width: 50%;
        }

        .results-cards-grid[data-view-mode="compact"] .result-card-col {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0.14rem;
        }

        .results-cards-grid[data-view-mode="compact"] .candidate-rank-card {
            min-height: 132px;
            padding: 0.42rem 0.3rem 0.38rem;
            border-radius: 12px;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(135, 156, 183, 0.2);
            box-shadow: none;
            overflow: visible;
        }

        .results-cards-grid[data-view-mode="compact"] .candidate-rank-card::before,
        .results-cards-grid[data-view-mode="compact"] .candidate-rank-card::after {
            display: none;
        }

        .results-cards-grid[data-view-mode="compact"] .candidate-photo {
            width: 72px;
            height: 72px;
            border: 3px solid #fff8e2;
            box-shadow:
                0 14px 22px rgba(17, 38, 61, 0.24),
                0 3px 0 rgba(255, 255, 255, 0.94) inset;
            transform: perspective(650px) rotateX(10deg) translateZ(8px);
        }

        .results-cards-grid[data-view-mode="compact"] .rank-frame {
            top: -0.38rem;
            left: 50%;
            transform: translateX(-50%);
            min-width: 56px;
            padding: 0.14rem 0.34rem;
            border-radius: 999px;
            z-index: 4;
        }

        .results-cards-grid[data-view-mode="compact"] .candidate-rank-card.rank-gold .rank-frame {
            background: linear-gradient(135deg, #ffe698 0%, #d79f17 100%) !important;
            border-color: #a77500 !important;
            color: #4e2f00 !important;
            box-shadow: 0 8px 16px rgba(167, 117, 0, 0.38) !important;
            transform: translateX(-50%) scale(1.05);
        }

        .results-cards-grid[data-view-mode="compact"] .candidate-rank-card.rank-silver .rank-frame {
            background: linear-gradient(135deg, #f6fbff 0%, #b7c5d5 100%) !important;
            border-color: #7b8a9b !important;
            color: #2f3b49 !important;
            box-shadow: 0 8px 16px rgba(92, 106, 123, 0.3) !important;
            transform: translateX(-50%) scale(1.03);
        }

        .results-cards-grid[data-view-mode="compact"] .candidate-rank-card.rank-bronze .rank-frame {
            background: linear-gradient(135deg, #ffd9c3 0%, #c97a48 100%) !important;
            border-color: #995126 !important;
            color: #51250f !important;
            box-shadow: 0 8px 16px rgba(153, 81, 38, 0.32) !important;
            transform: translateX(-50%) scale(1.02);
        }

        .results-cards-grid[data-view-mode="compact"] .candidate-rank-card.rank-gold .candidate-photo {
            border-color: #ffe7a8;
            box-shadow: 0 16px 24px rgba(130, 95, 20, 0.34), 0 3px 0 rgba(255, 255, 255, 0.94) inset;
        }

        .results-cards-grid[data-view-mode="compact"] .candidate-rank-card.rank-silver .candidate-photo {
            border-color: #e6edf5;
            box-shadow: 0 14px 22px rgba(92, 106, 123, 0.28), 0 3px 0 rgba(255, 255, 255, 0.94) inset;
        }

        .results-cards-grid[data-view-mode="compact"] .candidate-rank-card.rank-bronze .candidate-photo {
            border-color: #ffd9c3;
            box-shadow: 0 14px 22px rgba(153, 81, 38, 0.28), 0 3px 0 rgba(255, 255, 255, 0.94) inset;
        }

        .results-cards-grid[data-view-mode="compact"] .stats-frame {
            display: none;
        }

        .results-cards-grid[data-view-mode="compact"] .candidate-details {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0.3rem;
            width: 100%;
            justify-content: center;
            padding-inline-start: 0;
            z-index: 3;
        }

        .results-cards-grid[data-view-mode="compact"] .candidate-main-block {
            max-width: calc(100% - 0.45rem);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.24rem;
        }

        .results-cards-grid[data-view-mode="compact"] .candidate-name {
            margin: 0;
            max-width: 100%;
            padding: 0.14rem 0.56rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(188, 151, 56, 0.36);
            font-size: 0.78rem;
            font-weight: 900;
            line-height: 1.2;
        }

        .results-cards-grid[data-view-mode="compact"] .candidate-total-with-list-line {
            margin: 0;
            padding: 0;
            gap: 0;
        }

        .results-cards-grid[data-view-mode="compact"] .metric-label-total {
            display: none;
        }

        .results-cards-grid[data-view-mode="compact"] .totalWithListNum {
            min-width: 42px;
            margin-inline-start: 0;
            padding: 0.18rem 0.5rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 900;
            background: rgba(63, 83, 186, 0.2);
            border: 1px solid rgba(63, 83, 186, 0.36);
        }

        @media (min-width: 992px) {
            .results-cards-grid[data-view-mode="compact"] .result-card-col {
                flex: 0 0 25%;
                max-width: 25%;
            }
        }

        @media (min-width: 1400px) {
            .results-cards-grid[data-view-mode="compact"] .result-card-col {
                flex: 0 0 20%;
                max-width: 20%;
            }
        }

        .candidate-rank-card {
            position: relative;
            border-radius: 18px;
            border: 1px solid #ecd9a4;
            background: linear-gradient(165deg, #fffdf6 0%, #fff7e2 100%);
            box-shadow: var(--rp-shadow-soft);
            min-height: 122px;
            padding: 0.82rem 0.9rem;
            text-align: right;
            overflow: hidden;
            transition: box-shadow 0.22s ease, transform 0.22s ease, border-color 0.22s ease;
            display: flex;
            align-items: center;
            gap: 0.9rem;
        }

        .candidate-rank-card > * {
            position: relative;
            z-index: 2;
        }

        .candidate-rank-card::before {
            content: "";
            position: absolute;
            top: -58%;
            left: -45%;
            width: 34%;
            height: 220%;
            background: linear-gradient(110deg,
                    rgba(255, 255, 255, 0) 0%,
                    rgba(255, 255, 255, 0.06) 36%,
                    rgba(255, 248, 214, 0.74) 50%,
                    rgba(255, 255, 255, 0.08) 64%,
                    rgba(255, 255, 255, 0) 100%);
            transform: translateX(-160%) skewX(-18deg);
            opacity: 0;
            pointer-events: none;
            z-index: 1;
        }

        .candidate-rank-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 102% -8%, rgba(217, 119, 6, 0.15), transparent 43%);
            pointer-events: none;
        }

        .candidate-rank-card:hover {
            transform: translateY(-2px);
            border-color: #bdd8f5;
            box-shadow: 0 16px 30px rgba(17, 38, 61, 0.14);
        }

        .candidate-rank-card.rank-gold {
            border-color: #b98912;
            background: linear-gradient(145deg,
                    #fff8d2 0%,
                    #f8dc86 20%,
                    #d8aa32 46%,
                    #f1ca5d 70%,
                    #fff0bb 100%);
            box-shadow:
                0 16px 36px rgba(141, 97, 7, 0.34),
                inset 0 1px 0 rgba(255, 249, 223, 0.95),
                inset 0 -1px 0 rgba(152, 103, 9, 0.25);
        }

        .candidate-rank-card.rank-gold::before {
            opacity: 0.82;
            animation: goldCardShimmer 3.6s ease-in-out infinite;
        }

        .candidate-rank-card.rank-gold::after {
            background:
                radial-gradient(circle at 100% -12%, rgba(255, 240, 185, 0.58), transparent 44%),
                radial-gradient(circle at -8% 110%, rgba(173, 119, 6, 0.18), transparent 42%);
        }

        .candidate-rank-card.rank-silver {
            border-color: rgba(126, 141, 158, 0.6);
            background: linear-gradient(160deg, #f9fbfd 0%, #e6edf6 100%);
            box-shadow: 0 14px 30px rgba(89, 105, 124, 0.18);
        }

        .candidate-rank-card.rank-bronze {
            border-color: rgba(176, 109, 63, 0.62);
            background: linear-gradient(160deg, #fff2e7 0%, #f7d3b8 100%);
            box-shadow: 0 14px 30px rgba(150, 92, 53, 0.19);
        }

        .candidate-rank-card.rank-elite-4 {
            border-color: rgba(27, 109, 96, 0.44);
            background: linear-gradient(160deg, #ffffff 0%, #e5f8f5 100%);
        }

        .candidate-rank-card.rank-elite-5 {
            border-color: rgba(95, 73, 198, 0.42);
            background: linear-gradient(160deg, #ffffff 0%, #efe8ff 100%);
        }

        .candidate-rank-card.is-moving {
            box-shadow: 0 18px 42px rgba(0, 108, 103, 0.22);
        }

        .rank-frame {
            position: absolute;
            top: 0.62rem;
            left: 0.62rem;
            border-radius: 12px;
            padding: 0.28rem 0.4rem;
            border: 1px solid #cbddf3;
            background: #edf4ff;
            color: #134374;
            font-weight: 800;
            font-size: 0.74rem;
            letter-spacing: 0.01em;
            min-width: 64px;
            text-align: center;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.22rem;
        }

        .rank-stat-stack {
            display: flex;
            flex-direction: column;
            gap: 0.14rem;
            width: 100%;
        }

        .stats-frame {
            position: absolute;
            top: 0.62rem;
            right: 0.62rem;
            border-radius: 12px;
            padding: 0.28rem 0.38rem;
            border: 1px solid #cbddf3;
            background: #edf4ff;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 64px;
        }

        .rank-stat-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.16rem;
            border-radius: 999px;
            padding: 0.14rem 0.32rem;
            font-size: 0.66rem;
            font-weight: 900;
            line-height: 1;
            background: rgba(255, 255, 255, 0.7);
            color: #123b64;
            white-space: nowrap;
        }

        .rank-stat-chip .soundNum,
        .rank-stat-chip .listVotesNum {
            min-width: 0;
            margin-inline-start: 0;
            padding: 0;
            border-radius: 0;
            background: transparent !important;
            color: inherit !important;
            font-size: 0.72rem;
            font-weight: 900;
        }

        .rank-medal-icon {
            display: none;
            font-size: 0.72rem;
            line-height: 1;
        }

        .candidate-rank-card.rank-gold .rank-frame {
            border-color: #9d6d00;
            background: linear-gradient(135deg,
                    #fff3bf 0%,
                    #f2cf66 34%,
                    #ca960f 72%,
                    #ffe08d 100%);
            color: #4f2f00;
            box-shadow:
                0 8px 16px rgba(143, 98, 7, 0.34),
                inset 0 1px 0 rgba(255, 247, 215, 0.95);
        }

        .candidate-rank-card.rank-gold .rank-medal-icon {
            display: inline-flex;
            color: #684000;
            text-shadow: 0 1px 0 rgba(255, 241, 189, 0.82);
        }

        .candidate-rank-card.rank-gold .candidate-name {
            color: #5f3a00;
            text-shadow: 0 1px 0 rgba(255, 245, 204, 0.7);
        }

        .candidate-rank-card.rank-gold .soundNum {
            background: rgba(179, 120, 3, 0.18);
            color: #6b3f00;
        }

        .candidate-rank-card.rank-silver .rank-frame {
            border-color: #8e9cac;
            background: linear-gradient(135deg, #f4f7fb 0%, #bcc8d6 100%);
            color: #425467;
            box-shadow: 0 7px 14px rgba(99, 117, 138, 0.24);
        }

        .candidate-rank-card.rank-silver .rank-medal-icon {
            display: inline-flex;
            color: #566578;
        }

        .candidate-rank-card.rank-bronze .rank-frame {
            border-color: #a46134;
            background: linear-gradient(135deg, #fdd8bb 0%, #cf8352 100%);
            color: #5d2f12;
            box-shadow: 0 7px 14px rgba(164, 97, 52, 0.26);
        }

        .candidate-rank-card.rank-bronze .rank-medal-icon {
            display: inline-flex;
            color: #6c3718;
        }

        .candidate-rank-card.rank-elite-4 .rank-frame {
            border-color: rgba(27, 109, 96, 0.44);
            background: rgba(27, 109, 96, 0.14);
            color: #12584e;
        }

        .candidate-rank-card.rank-elite-5 .rank-frame {
            border-color: rgba(95, 73, 198, 0.44);
            background: rgba(95, 73, 198, 0.14);
            color: #432ea8;
        }

        .candidate-photo {
            width: 92px;
            height: 92px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 8px 18px rgba(17, 38, 61, 0.16);
            object-fit: cover;
            background: #eaf1fb;
            flex-shrink: 0;
        }

        .candidate-details {
            min-width: 0;
            width: auto;
            flex: 1 1 auto;
            padding-inline-start: 0.28rem;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 0.7rem;
        }

        .candidate-main-block {
            min-width: 0;
            flex: 0 1 auto;
            max-width: 62%;
        }

        .candidate-side-stats {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.26rem;
            text-align: center;
            flex: 0 0 auto;
            margin-inline-start: 0.2rem;
            padding: 0.38rem 0.46rem;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.56);
            border: 1px solid rgba(150, 169, 192, 0.24);
        }

        .candidate-name {
            margin: 0 0 0.35rem;
            font-size: 1.04rem;
            font-weight: 800;
            color: #123150;
            min-height: 0;
            line-height: 1.45;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .candidate-votes-line {
            margin: 0;
            color: #4e647f;
            font-size: 0.88rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.26rem;
        }

        .candidate-list-votes-line {
            margin: 0;
            color: #5f6f84;
            font-size: 0.82rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.26rem;
        }

        .candidate-total-with-list-line {
            margin: 0.12rem 0 0;
            color: #354b63;
            font-size: 0.88rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 0.26rem;
        }

        .metric-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.1rem;
            height: 1.5rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 900;
            letter-spacing: 0.01em;
        }

        .metric-label-total {
            background: rgba(63, 83, 186, 0.13);
            color: #3345a0;
        }

        .metric-label-votes {
            background: rgba(0, 108, 103, 0.12);
            color: #005b56;
        }

        .metric-label-list {
            background: rgba(135, 96, 13, 0.14);
            color: #7a4b00;
        }

        .totalWithListNum {
            display: inline-flex;
            min-width: 56px;
            justify-content: center;
            align-items: center;
            border-radius: 999px;
            padding: 0.24rem 0.52rem;
            margin-inline-start: 0.25rem;
            background: rgba(63, 83, 186, 0.14);
            color: #3345a0;
            font-size: 0.94rem;
            font-weight: 900;
            transition: transform 0.22s ease, background-color 0.22s ease;
        }

        .candidate-meta-line {
            margin: 0.22rem 0 0;
            color: #607792;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .soundNum {
            display: inline-flex;
            min-width: 56px;
            justify-content: center;
            align-items: center;
            border-radius: 999px;
            padding: 0.24rem 0.52rem;
            margin-inline-start: 0.25rem;
            background: rgba(0, 108, 103, 0.14);
            color: #005b56;
            font-size: 0.94rem;
            font-weight: 900;
            transition: transform 0.22s ease, background-color 0.22s ease;
        }

        .listVotesNum {
            display: inline-flex;
            min-width: 56px;
            justify-content: center;
            align-items: center;
            border-radius: 999px;
            padding: 0.24rem 0.52rem;
            margin-inline-start: 0.25rem;
            background: rgba(135, 96, 13, 0.12);
            color: #7a4b00;
            font-size: 0.92rem;
            font-weight: 900;
            transition: transform 0.22s ease, background-color 0.22s ease;
        }

        .soundNum.is-updated {
            transform: scale(1.08);
            background: rgba(217, 119, 6, 0.2);
            color: #8f4b05;
        }

        .totalWithListNum.is-updated {
            transform: scale(1.1);
            background: rgba(217, 119, 6, 0.22);
            color: #8f4b05;
        }

        .results-modal .modal-content {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 20px 44px rgba(17, 38, 61, 0.2);
            overflow: hidden;
        }

        .results-modal .modal-header {
            border-bottom-color: #dbe8f8;
            background: linear-gradient(140deg, #f5f9ff 0%, #ffffff 100%);
        }

        .results-modal .table {
            margin-bottom: 0;
            font-size: 0.89rem;
        }

        .results-modal .table thead th,
        .results-modal .table thead td {
            background: #102f4f;
            color: #fff;
            font-weight: 800;
            border-color: #2a4d71;
            white-space: nowrap;
            vertical-align: middle;
            padding: 0.55rem 0.5rem;
        }

        .results-modal .table tbody td {
            border-color: #dce8f6;
            font-weight: 700;
            color: #1a3d61;
            padding: 0.46rem;
            vertical-align: middle;
        }

        .results-modal .table tbody tr:hover td {
            background: #f7fbff;
        }

        @media (max-width: 767.98px) {
            .results-hero {
                border-radius: 16px;
                padding: 0.85rem;
            }

            .results-open-details {
                width: 100%;
            }

            .candidate-rank-card {
                min-height: 112px;
                padding: 0.68rem 0.74rem;
                gap: 0.62rem;
            }

            .candidate-photo {
                width: 76px;
                height: 76px;
            }

            .candidate-name {
                font-size: 0.92rem;
            }

            .candidate-votes-line {
                font-size: 0.8rem;
            }

            .candidate-list-votes-line {
                font-size: 0.78rem;
            }

            .candidate-details {
                gap: 0.42rem;
            }

            .results-cards-grid[data-view-mode="grid"] .result-card-col {
                flex: 0 0 50%;
                max-width: 50%;
            }

            .results-cards-grid[data-view-mode="grid"] {
                row-gap: 0.56rem;
            }

            .results-cards-grid[data-view-mode="grid"] .candidate-rank-card {
                min-height: 160px;
                padding: 0.48rem 0.48rem 2.05rem;
                border-radius: 12px;
                gap: 0.32rem;
                flex-direction: column;
                align-items: stretch;
                text-align: center;
                overflow: visible;
            }

            .results-cards-grid[data-view-mode="grid"] .candidate-photo {
                width: 58px;
                height: 58px;
                margin: 0 auto;
            }

            .results-cards-grid[data-view-mode="grid"] .rank-frame {
                top: 0.36rem;
                left: 0.36rem;
                min-width: 54px;
                padding: 0.2rem 0.28rem;
            }

            .results-cards-grid[data-view-mode="grid"] .stats-frame {
                top: 0.36rem;
                right: 0.36rem;
                min-width: 54px;
                padding: 0.2rem 0.28rem;
            }

            .results-cards-grid[data-view-mode="grid"] .rank-stat-chip {
                font-size: 0.62rem;
                padding: 0.12rem 0.22rem;
            }

            .results-cards-grid[data-view-mode="grid"] .candidate-details {
                width: 100%;
                display: block;
                justify-content: center;
                padding-inline-start: 0;
                gap: 0;
            }

            .results-cards-grid[data-view-mode="grid"] .candidate-main-block {
                max-width: 100%;
                flex: none;
            }

            .results-cards-grid[data-view-mode="grid"] .candidate-name {
                font-size: 0.92rem;
                line-height: 1.35;
                white-space: normal;
                overflow: visible;
                text-overflow: clip;
                margin-bottom: 0.05rem;
            }

            .results-cards-grid[data-view-mode="grid"] .candidate-total-with-list-line {
                position: absolute;
                left: 50%;
                bottom: -0.62rem;
                transform: translateX(-50%);
                justify-content: center;
                font-size: 0.72rem;
                margin: 0;
                z-index: 3;
            }

            .results-cards-grid[data-view-mode="grid"] .metric-label-total {
                display: none;
            }

            .results-cards-grid[data-view-mode="grid"] .totalWithListNum {
                min-width: 46px;
                font-size: 0.92rem;
                padding: 0.24rem 0.7rem;
                border-radius: 10px 10px 14px 14px;
                border: 1px solid rgba(63, 83, 186, 0.32);
                box-shadow: 0 10px 16px rgba(63, 83, 186, 0.2);
            }

            .results-cards-grid[data-view-mode="compact"] .result-card-col {
                flex: 0 0 33.3333%;
                max-width: 33.3333%;
            }
        }

        @media (min-width: 1200px) {
            .results-cards-grid[data-view-mode="grid"] .result-card-col {
                flex: 0 0 33.3333%;
                max-width: 33.3333%;
            }
        }

        @media (min-width: 768px) {
            .results-cards-grid[data-view-mode="grid"] .stats-frame {
                left: 0.62rem;
                right: auto;
                top: 3.1rem;
            }
        }

        @media (min-width: 992px) {
            .results-cards-grid[data-view-mode="long"] .stats-frame {
                left: 0.62rem;
                right: auto;
                top: 3.1rem;
            }
        }

        @keyframes goldCardShimmer {
            0% {
                transform: translateX(-160%) skewX(-18deg);
            }

            54% {
                transform: translateX(370%) skewX(-18deg);
            }

            100% {
                transform: translateX(370%) skewX(-18deg);
            }
        }

        @media (max-width: 991px) {
            body.results-auto-chrome-hidden .dashboard-topbar-mobile {
                transform: translateY(calc(-100% - 14px));
                opacity: 0;
                pointer-events: none;
            }

            body.results-auto-chrome-hidden .dashboard-mobilebar {
                transform: translateY(calc(100% + 14px));
                opacity: 0;
                pointer-events: none;
            }

            .dashboard-topbar-mobile,
            .dashboard-mobilebar {
                transition: transform 0.26s ease, opacity 0.22s ease;
            }
        }
    </style>

    <section class="results-pro-page">
        <div class="container-fluid results-shell">
            <div class="results-hero rtl">
                <div>
                    <h1 class="results-title">النتائج العامة المباشرة</h1>
                    <p class="results-subtitle">تابع الآن نتائج الانتخابات مع تحديث لحظي مباشر ودقة عالية.</p>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="results-meta-badge">
                        <i class="fa-solid fa-signal"></i>
                        <span>Live Ranking</span>
                    </span>

                    <span class="results-contact">
                        <i class="fa-brands fa-whatsapp"></i>
                        55150551
                    </span>

                    <button type="button" id="resultsViewSwitchBtn" class="results-view-switch-btn">
                        طريقة العرض: العرض الطولى
                    </button>

                    <button data-bs-toggle="modal" data-bs-target="#displayData" class="results-open-details">
                        عرض تفاصيل اللجان
                    </button>
                </div>
            </div>

            <div class="modal modal-xl rtl results-modal" id="displayData" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="mb-0 fw-bold">تفاصيل أصوات اللجان</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="table-responsive mt-2">
                                <table class="table table-bordered rtl overflow-hidden rounded-3 text-center">
                                    <thead class="border-0 border-secondary border-bottom border-2">
                                        <tr>
                                            <th class="w150"></th>
                                            @foreach ($schools as $school)
                                                <th colspan="{{ $committees->count() / 2 + 1 }}">
                                                    {{ $school->name . ' ' . '(' . $school->type . ')' }}
                                                </th>
                                            @endforeach
                                            <th></th>
                                        </tr>

                                        <tr>
                                            <td>الاسم</td>
                                            <td>مج</td>
                                            @foreach ($committees as $committee)
                                                @if ($committee->type == App\Enums\Type::MEN->value)
                                                    <td>{{ $committee->name }}</td>
                                                @endif
                                            @endforeach
                                            <td>مج</td>
                                            @foreach ($committees as $committee)
                                                @if ($committee->type == App\Enums\Type::WOMEN->value)
                                                    <td>{{ $committee->name }}</td>
                                                @endif
                                            @endforeach
                                            <td>الأصوات</td>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($candidates as $candidate)
                                            @if ($candidate->committees->isNotEmpty())
                                                <tr data-candidate-row-id="{{ $candidate->id }}">
                                                    <td>{{ $candidate->user->name }}</td>

                                                    <td class="table-primary candidate-men-total">
                                                        {{ $candidate->committees->where('type', App\Enums\Type::MEN->value)->sum('pivot.votes') }}
                                                    </td>

                                                    @foreach ($candidate->committees->where('type', App\Enums\Type::MEN->value) as $committee)
                                                        <td data-candidate-committee-id="{{ $candidate->id }}" data-committee-id="{{ $committee->id }}">{{ $committee->pivot->votes }}</td>
                                                    @endforeach

                                                    <td class="table-primary candidate-women-total">
                                                        {{ $candidate->committees->where('type', App\Enums\Type::WOMEN->value)->sum('pivot.votes') }}
                                                    </td>

                                                    @foreach ($candidate->committees->where('type', App\Enums\Type::WOMEN->value) as $committee)
                                                        <td data-candidate-committee-id="{{ $candidate->id }}" data-committee-id="{{ $committee->id }}">{{ $committee->pivot->votes }}</td>
                                                    @endforeach

                                                    <td class="table-danger candidate-total-votes">{{ $candidate->votes }}</td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        <tr class="table-secondary">
                                            <td>** المجموع **</td>

                                            <td class="table-primary" id="allResultMenTotalAll">
                                                {{ $committees->where('type', App\Enums\Type::MEN->value)->flatMap(function ($committee) {
                                                        return $committee->candidates;
                                                    })->sum('pivot.votes') }}
                                            </td>

                                            @foreach ($committees as $committee)
                                                @if ($committee->type == App\Enums\Type::MEN->value)
                                                    <td data-total-committee-id="{{ $committee->id }}">{{ $committee->candidates->sum('pivot.votes') }}</td>
                                                @endif
                                            @endforeach

                                            <td class="table-primary" id="allResultWomenTotalAll">
                                                {{ $committees->where('type', App\Enums\Type::WOMEN->value)->flatMap(function ($committee) {
                                                        return $committee->candidates;
                                                    })->sum('pivot.votes') }}
                                            </td>

                                            @foreach ($committees as $committee)
                                                @if ($committee->type == App\Enums\Type::WOMEN->value)
                                                    <td data-total-committee-id="{{ $committee->id }}">{{ $committee->candidates->sum('pivot.votes') }}</td>
                                                @endif
                                            @endforeach

                                            <td class="table-danger" id="allResultGrandTotalAll">
                                                {{ $committees->sum(function ($committee) {
                                                    return $committee->candidates->sum('pivot.votes');
                                                }) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row rtl justify-content-center results-cards-grid" id="allResultsCardsGrid" data-view-mode="long">
                @php
                    $listLeaderVoteTotals = is_array($listLeaderVoteTotals ?? null) ? $listLeaderVoteTotals : [];
                    $sortedCandidates = $candidates
                        ->sortByDesc(function ($candidate) use ($listLeaderVoteTotals) {
                            $candidateType = (string) ($candidate->candidate_type ?? '');
                            $listGroupId = $candidateType === 'list_leader'
                                ? (int) $candidate->id
                                : (int) ($candidate->list_leader_candidate_id ?? 0);
                            $listTotalVotes = (int) ($listGroupId > 0 ? ($listLeaderVoteTotals[$listGroupId] ?? 0) : 0);
                            $candidateVotes = (int) $candidate->committees->sum('pivot.votes');

                            return $candidateVotes + $listTotalVotes;
                        })
                        ->values();
                @endphp
                @foreach ($sortedCandidates as $i => $can)
                    @php
                        $rankClass = $i === 0
                            ? 'rank-gold'
                            : ($i === 1
                                ? 'rank-silver'
                                : ($i === 2
                                    ? 'rank-bronze'
                                    : ($i === 3
                                        ? 'rank-elite-4'
                                        : ($i === 4 ? 'rank-elite-5' : ''))));
                        $candidateType = (string) ($can->candidate_type ?? '');
                        $listGroupId = $candidateType === 'list_leader'
                            ? (int) $can->id
                            : (int) ($can->list_leader_candidate_id ?? 0);
                        $listTotalVotes = (int) ($listGroupId > 0 ? ($listLeaderVoteTotals[$listGroupId] ?? 0) : 0);
                        $candidateVotes = (int) $can->committees->sum('pivot.votes');
                        $totalWithListVotes = (int) ($candidateVotes + $listTotalVotes);
                    @endphp
                    <div class="col-12 result-card-col" data-candidate-id="{{ $can->id }}">
                        <article class="candidate-rank-card {{ $rankClass }}">
                            <div class="rank-frame">
                                <i class="fa-solid fa-medal rank-medal-icon" aria-hidden="true"></i>
                                <span class="rank-label">{{ $i + 1 }}</span>
                            </div>
                            <div class="stats-frame">
                                <div class="rank-stat-stack">
                                    <span class="rank-stat-chip">أ.ف <span class="soundNum">{{ $candidateVotes }}</span></span>
                                    <span class="rank-stat-chip">أ.ق <span class="listVotesNum">{{ $listTotalVotes }}</span></span>
                                </div>
                            </div>

                            <img src="{{ $can->user->image ?? asset('assets/admin/images/images.png') }}" class="candidate-photo" alt="candidate image" />

                            <div class="candidate-details">
                                <div class="candidate-main-block">
                                    <h6 class="candidate-name">{{ $can->user->name }}</h6>
                                    <p class="candidate-total-with-list-line"><span class="metric-label metric-label-total">مجموع الأصوات</span> <span class="totalWithListNum">{{ $totalWithListVotes }}</span></p>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script>
        (function () {
            var electionId = @json((int) ($election_id ?? 0));
            var liveStatsUrl = @json(route('all.results.live-stats'));
            var fallbackTimer = null;
            var realtimeChannelName = null;
            var inFlight = false;
            var cardsGrid = document.getElementById('allResultsCardsGrid');
            var viewSwitchBtn = document.getElementById('resultsViewSwitchBtn');
            var viewModeStorageKey = 'all_results_view_mode';
            var viewModes = [
                { key: 'long', label: 'العرض الطولى' },
                { key: 'grid', label: 'العرض الشبكى' },
                { key: 'compact', label: 'العرض المختصر' }
            ];

            function ordinal(rank) {
                if (rank % 100 >= 11 && rank % 100 <= 13) {
                    return rank + 'th';
                }

                switch (rank % 10) {
                    case 1: return rank + 'st';
                    case 2: return rank + 'nd';
                    case 3: return rank + 'rd';
                    default: return rank + 'th';
                }
            }

            function isValidViewMode(mode) {
                return viewModes.some(function (viewMode) {
                    return viewMode.key === mode;
                });
            }

            function getViewModeLabel(mode) {
                var selectedMode = viewModes.find(function (viewMode) {
                    return viewMode.key === mode;
                });
                return selectedMode ? selectedMode.label : 'العرض الطولى';
            }

            function applyViewMode(mode) {
                if (!cardsGrid) {
                    return;
                }

                var safeMode = isValidViewMode(mode) ? mode : 'long';
                cardsGrid.setAttribute('data-view-mode', safeMode);
                if (viewSwitchBtn) {
                    viewSwitchBtn.innerText = 'طريقة العرض: ' + getViewModeLabel(safeMode);
                }
                try {
                    localStorage.setItem(viewModeStorageKey, safeMode);
                } catch (e) {
                    // ignore storage issues
                }
            }

            function loadInitialViewMode() {
                var storedMode = 'long';
                try {
                    storedMode = localStorage.getItem(viewModeStorageKey) || 'long';
                } catch (e) {
                    storedMode = 'long';
                }
                applyViewMode(storedMode);
            }

            function cycleViewMode() {
                if (!cardsGrid) {
                    return;
                }

                var currentMode = cardsGrid.getAttribute('data-view-mode') || 'long';
                var currentIndex = viewModes.findIndex(function (viewMode) {
                    return viewMode.key === currentMode;
                });
                var nextIndex = currentIndex < 0 ? 0 : (currentIndex + 1) % viewModes.length;
                applyViewMode(viewModes[nextIndex].key);
            }

            function voteFromCardCol(cardCol) {
                var voteNode = cardCol.querySelector('.totalWithListNum');
                return parseInt((voteNode ? voteNode.innerText : '0'), 10) || 0;
            }

            function markRankClasses(sortedCols) {
                var rankClasses = ['rank-gold', 'rank-silver', 'rank-bronze', 'rank-elite-4', 'rank-elite-5'];

                sortedCols.forEach(function (cardCol, index) {
                    var card = cardCol.querySelector('.candidate-rank-card');
                    var rankLabel = cardCol.querySelector('.rank-label');
                    if (!card) {
                        return;
                    }

                    rankClasses.forEach(function (className) {
                        card.classList.remove(className);
                    });

                    if (index < rankClasses.length) {
                        card.classList.add(rankClasses[index]);
                    }

                    if (rankLabel) {
                        rankLabel.innerText = ordinal(index + 1);
                    }

                });
            }

            function animateGridReorder(sortedCols) {
                if (!cardsGrid) {
                    return;
                }

                var firstRects = new Map();
                Array.from(cardsGrid.children).forEach(function (col) {
                    firstRects.set(col, col.getBoundingClientRect());
                });

                sortedCols.forEach(function (col) {
                    cardsGrid.appendChild(col);
                });

                sortedCols.forEach(function (col) {
                    var first = firstRects.get(col);
                    var last = col.getBoundingClientRect();

                    if (!first || !last) {
                        return;
                    }

                    var dx = first.left - last.left;
                    var dy = first.top - last.top;

                    if (dx || dy) {
                        col.style.transition = 'none';
                        col.style.transform = 'translate(' + dx + 'px, ' + dy + 'px)';

                        void col.offsetWidth;

                        col.style.transition = 'transform 760ms cubic-bezier(0.2, 0.85, 0.2, 1)';
                        col.style.transform = 'translate(0, 0)';

                        var card = col.querySelector('.candidate-rank-card');
                        if (card) {
                            card.classList.add('is-moving');
                            setTimeout(function () {
                                card.classList.remove('is-moving');
                            }, 850);
                        }
                    }
                });

                markRankClasses(sortedCols);
            }

            function updateCandidatesGridByVotes() {
                if (!cardsGrid) {
                    return;
                }

                var currentCols = Array.from(cardsGrid.children);
                var sortedCols = currentCols.slice().sort(function (a, b) {
                    return voteFromCardCol(b) - voteFromCardCol(a);
                });

                var changedOrder = sortedCols.some(function (col, idx) {
                    return col !== currentCols[idx];
                });

                if (changedOrder) {
                    animateGridReorder(sortedCols);
                } else {
                    markRankClasses(sortedCols);
                }
            }

            function applyUpdatedStyle(node) {
                if (!node) {
                    return;
                }

                node.classList.add('is-updated');
                setTimeout(function () {
                    node.classList.remove('is-updated');
                }, 420);
            }

            function applyAllResultsStats(payload) {
                if (!payload || payload.success !== true || !Array.isArray(payload.candidates)) {
                    return;
                }

                payload.candidates.forEach(function (candidate) {
                    var candidateId = parseInt(candidate.id, 10) || 0;
                    if (!candidateId) {
                        return;
                    }

                    var votes = parseInt(candidate.votes, 10) || 0;
                    var menTotal = parseInt(candidate.men_total, 10) || 0;
                    var womenTotal = parseInt(candidate.women_total, 10) || 0;
                    var listTotalVotes = candidate.list_total_votes;
                    var totalWithListVotes = parseInt(candidate.total_with_list_votes, 10) || 0;

                    var cardCol = document.querySelector('[data-candidate-id="' + candidateId + '"]');
                    if (cardCol) {
                        var soundNum = cardCol.querySelector('.soundNum');
                        if (soundNum) {
                            soundNum.innerText = votes;
                        }

                        var listVotesNum = cardCol.querySelector('.listVotesNum');
                        if (listVotesNum) {
                            listVotesNum.innerText = parseInt(listTotalVotes, 10) || 0;
                        }

                        var totalWithListNum = cardCol.querySelector('.totalWithListNum');
                        if (totalWithListNum) {
                            var oldTotalWithListVotes = parseInt(totalWithListNum.innerText, 10) || 0;
                            totalWithListNum.innerText = totalWithListVotes;
                            if (oldTotalWithListVotes !== totalWithListVotes) {
                                applyUpdatedStyle(totalWithListNum);
                            }
                        }
                    }

                    var row = document.querySelector('tr[data-candidate-row-id="' + candidateId + '"]');
                    if (row) {
                        var menCell = row.querySelector('.candidate-men-total');
                        var womenCell = row.querySelector('.candidate-women-total');
                        var totalCell = row.querySelector('.candidate-total-votes');

                        if (menCell) menCell.innerText = menTotal;
                        if (womenCell) womenCell.innerText = womenTotal;
                        if (totalCell) totalCell.innerText = votes;
                    }

                    var committeeVotes = candidate.committee_votes || {};
                    Object.keys(committeeVotes).forEach(function (committeeId) {
                        var committeeValue = parseInt(committeeVotes[committeeId], 10) || 0;
                        var selector = '[data-candidate-committee-id="' + candidateId + '"][data-committee-id="' + committeeId + '"]';
                        var committeeCell = document.querySelector(selector);
                        if (committeeCell) {
                            committeeCell.innerText = committeeValue;
                        }
                    });
                });

                var committeeTotals = payload.committee_totals || {};
                Object.keys(committeeTotals).forEach(function (committeeId) {
                    var totalValue = parseInt(committeeTotals[committeeId], 10) || 0;
                    var totalCells = document.querySelectorAll('[data-total-committee-id="' + committeeId + '"]');
                    totalCells.forEach(function (cell) {
                        cell.innerText = totalValue;
                    });
                });

                var menTotalAll = document.getElementById('allResultMenTotalAll');
                if (menTotalAll) {
                    menTotalAll.innerText = parseInt(payload.men_total_all, 10) || 0;
                }

                var womenTotalAll = document.getElementById('allResultWomenTotalAll');
                if (womenTotalAll) {
                    womenTotalAll.innerText = parseInt(payload.women_total_all, 10) || 0;
                }

                var grandTotalAll = document.getElementById('allResultGrandTotalAll');
                if (grandTotalAll) {
                    grandTotalAll.innerText = parseInt(payload.grand_total_all, 10) || 0;
                }

                updateCandidatesGridByVotes();
            }

            function fetchAllResultsStats() {
                if (inFlight || !electionId) {
                    return;
                }

                inFlight = true;

                axios.get(liveStatsUrl, {
                    params: {
                        election_id: electionId,
                    },
                    headers: {
                        'Accept': 'application/json',
                    }
                }).then(function (response) {
                    applyAllResultsStats(response.data || {});
                }).catch(function () {
                    // Silent fail for background refresh.
                }).finally(function () {
                    inFlight = false;
                });
            }

            function startRealtime() {
                if (!electionId) {
                    return;
                }

                fetchAllResultsStats();
                updateCandidatesGridByVotes();

                if (window.Echo && typeof window.Echo.channel === 'function') {
                    realtimeChannelName = 'results.' + electionId;
                    window.Echo.channel(realtimeChannelName).listen('.sorting.realtime.updated', function () {
                        fetchAllResultsStats();
                    });
                }

                if (fallbackTimer) {
                    clearInterval(fallbackTimer);
                }

                fallbackTimer = setInterval(function () {
                    if (!document.hidden) {
                        fetchAllResultsStats();
                    }
                }, 2500);
            }

            window.addEventListener('beforeunload', function () {
                if (realtimeChannelName && window.Echo && typeof window.Echo.leave === 'function') {
                    window.Echo.leave(realtimeChannelName);
                }

                if (fallbackTimer) {
                    clearInterval(fallbackTimer);
                }
            });

            document.addEventListener('visibilitychange', function () {
                if (!document.hidden) {
                    fetchAllResultsStats();
                }
            });

            loadInitialViewMode();
            if (viewSwitchBtn) {
                viewSwitchBtn.addEventListener('click', cycleViewMode);
            }

            startRealtime();
        })();
    </script>
    <script>
        (function () {
            var page = document.querySelector('.results-pro-page');
            if (!page || !document.body) {
                return;
            }

            var root = document.documentElement;
            var body = document.body;
            var mobileMedia = window.matchMedia ? window.matchMedia('(max-width: 991px)') : null;
            var idleTimer = null;
            var hiddenClass = 'results-auto-chrome-hidden';
            var topGap = 12;
            var bottomGap = 12;

            function isMobile() {
                return !!(mobileMedia && mobileMedia.matches);
            }

            function setVisibleOffsets() {
                var topBar = document.querySelector('.dashboard-topbar-mobile');
                var bottomBar = document.querySelector('.dashboard-mobilebar');

                if (topBar) {
                    var topRect = topBar.getBoundingClientRect();
                    root.style.setProperty('--dashboard-topbar-offset', Math.ceil(topRect.bottom + topGap) + 'px');
                }

                if (bottomBar) {
                    var bottomRect = bottomBar.getBoundingClientRect();
                    var bottomOffset = Math.ceil((window.innerHeight - bottomRect.top) + bottomGap);
                    root.style.setProperty('--dashboard-mobilebar-offset', bottomOffset + 'px');
                }
            }

            function showChrome() {
                body.classList.remove(hiddenClass);
                setVisibleOffsets();
            }

            function hideChrome() {
                body.classList.add(hiddenClass);
                root.style.setProperty('--dashboard-topbar-offset', '12px');
                root.style.setProperty('--dashboard-mobilebar-offset', '16px');
            }

            function clearIdleTimer() {
                if (!idleTimer) {
                    return;
                }
                clearTimeout(idleTimer);
                idleTimer = null;
            }

            function queueAutoHide() {
                clearIdleTimer();
                if (!isMobile()) {
                    return;
                }
                idleTimer = setTimeout(function () {
                    hideChrome();
                }, 3000);
            }

            function handleActivity() {
                if (!isMobile()) {
                    showChrome();
                    return;
                }
                showChrome();
                queueAutoHide();
            }

            function handleResize() {
                if (!isMobile()) {
                    clearIdleTimer();
                    body.classList.remove(hiddenClass);
                    setVisibleOffsets();
                    return;
                }
                handleActivity();
            }

            window.addEventListener('scroll', handleActivity, { passive: true });
            window.addEventListener('touchmove', handleActivity, { passive: true });
            window.addEventListener('resize', handleResize);
            document.addEventListener('visibilitychange', function () {
                if (!document.hidden) {
                    handleResize();
                }
            });

            handleResize();
        })();
    </script>
@endpush
