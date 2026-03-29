@extends('layouts.dashboard.app')

@section('content')
<style>
  .sorting-page {
    --sp-ink: #102a43;
    --sp-muted: #486581;
    --sp-soft: #f5f8fc;
    --sp-primary: #0f766e;
    --sp-primary-dark: #0b5f58;
    --sp-accent: #f59e0b;
    --sp-danger: #d64545;
    --sp-success: #1f9d66;
    --sp-border: #d9e2ec;
    --sp-card-shadow: 0 12px 35px rgba(15, 118, 110, 0.14);
    --sp-font-main: "Cairo", "Tajawal", sans-serif;
    --sorting-bulk-sticky-top: 8px;

    position: relative;
    padding: 1.2rem 0 2rem;
    font-family: var(--sp-font-main);
    color: var(--sp-ink);
  }

  .sorting-page::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: 0;
    background:
      radial-gradient(circle at 88% 10%, rgba(245, 158, 11, 0.14), transparent 42%),
      radial-gradient(circle at 12% 16%, rgba(15, 118, 110, 0.13), transparent 40%),
      linear-gradient(180deg, #f9fbff 0%, #eef5f8 100%);
  }

  .sorting-shell {
    position: relative;
    z-index: 2;
  }

  .sorting-card {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid var(--sp-border);
    border-radius: 20px;
    box-shadow: var(--sp-card-shadow);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
  }

  .sorting-hero {
    padding: 1.15rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    animation: spSlideDown 0.42s ease;
  }

  .sorting-title {
    margin: 0;
    font-size: clamp(1.1rem, 2.4vw, 1.55rem);
    font-weight: 800;
    color: var(--sp-ink);
    line-height: 1.65;
  }

  .sorting-subtitle {
    margin: 0.35rem 0 0;
    color: var(--sp-muted);
    font-size: 0.93rem;
  }

  .sorting-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.5rem 0.85rem;
    border-radius: 999px;
    background: rgba(15, 118, 110, 0.1);
    color: var(--sp-primary-dark);
    font-weight: 700;
    font-size: 0.83rem;
    white-space: nowrap;
  }

  .sorting-toolbar {
    margin-top: 1rem;
    padding: 1rem;
    display: grid;
    grid-template-columns: minmax(260px, 1fr) minmax(240px, 1fr);
    gap: 0.9rem;
    align-items: end;
    animation: spFadeUp 0.5s ease;
  }

  .sorting-field {
    display: flex;
    flex-direction: column;
    gap: 0.42rem;
  }

  .sorting-label {
    margin: 0;
    font-size: 0.82rem;
    color: var(--sp-muted);
    font-weight: 700;
  }

  .sorting-input,
  .sorting-select {
    height: 46px;
    border-radius: 12px;
    border: 1px solid #bfccd9;
    background: #fff;
    color: var(--sp-ink);
    font-weight: 700;
    font-size: 0.95rem;
    padding: 0.45rem 0.85rem;
    transition: all 0.2s ease;
  }

  .sorting-input:focus,
  .sorting-select:focus {
    border-color: rgba(15, 118, 110, 0.7);
    box-shadow: 0 0 0 0.2rem rgba(15, 118, 110, 0.18);
    outline: none;
  }

  .sorting-sort-inline {
    display: grid;
    grid-template-columns: minmax(130px, 1fr) minmax(100px, 0.8fr);
    gap: 0.55rem;
  }

  .sorting-sort-inline .sorting-select {
    font-size: 0.88rem;
  }

  .named-sort-panel {
    margin-top: 0.75rem;
    padding: 0.75rem;
    border: 1px dashed #bcd2e8;
    border-radius: 12px;
    background: #f8fbff;
    display: none;
  }

  .named-sort-grid {
    display: grid;
    grid-template-columns: minmax(150px, 1fr) minmax(170px, 1fr) auto;
    gap: 0.55rem;
    align-items: center;
  }

  .named-sort-help {
    margin: 0.45rem 0 0;
    color: #486581;
    font-size: 0.78rem;
    font-weight: 700;
  }

  .named-sort-actions {
    margin-top: 0.55rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    align-items: center;
  }

  .btn-named-sort-aux {
    border: 1px solid #b8cde2;
    background: #fff;
    color: #1f4369;
    border-radius: 9px;
    min-height: 36px;
    padding: 0.35rem 0.75rem;
    font-size: 0.78rem;
    font-weight: 800;
  }

  .btn-named-sort-aux.active {
    background: linear-gradient(135deg, #0f766e 0%, #159e90 100%);
    border-color: #0f766e;
    color: #fff;
  }

  .quick-assign-status {
    color: #334e68;
    font-size: 0.78rem;
    font-weight: 700;
  }

  .btn-named-sort-save {
    border: 0;
    border-radius: 11px;
    height: 46px;
    min-width: 150px;
    color: #fff;
    font-weight: 800;
    background: linear-gradient(135deg, #1d4ed8 0%, #1e64f0 100%);
  }

  .order-display {
    display: inline-flex;
    min-width: 34px;
    justify-content: center;
  }

  .custom-order-input {
    width: 70px;
    height: 34px;
    border-radius: 8px;
    border: 1px solid #b8cde2;
    text-align: center;
    font-weight: 800;
    display: none;
    margin: 0 auto;
  }

  tr.quick-assign-target {
    box-shadow: inset 0 0 0 2px rgba(15, 118, 110, 0.22);
  }

  .search-wrap {
    position: relative;
  }

  .search-wrap i {
    position: absolute;
    top: 50%;
    left: 0.85rem;
    transform: translateY(-50%);
    color: #7b8794;
    pointer-events: none;
  }

  .search-wrap .sorting-input {
    padding-left: 2.2rem;
  }

  .mobile-quick-access {
    display: none;
  }

  .quick-letter-filter,
  .recent-candidates-chips {
    display: flex;
    gap: 0.4rem;
    overflow-x: auto;
    padding-bottom: 0.2rem;
  }

  .quick-category-filter {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.4rem;
  }

  .filter-mode-switch {
    margin-top: 0.8rem;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem;
    border: 1px solid #d7e2ee;
    border-radius: 12px;
    background: #fff;
  }

  .filter-mode-btn {
    border: 0;
    border-radius: 9px;
    min-height: 34px;
    min-width: 88px;
    padding: 0.25rem 0.65rem;
    font-size: 0.78rem;
    font-weight: 800;
    color: #334e68;
    background: transparent;
    transition: all 0.16s ease;
  }

  .filter-mode-btn.active {
    color: #fff;
    background: linear-gradient(135deg, #0f766e 0%, #159e90 100%);
    box-shadow: 0 7px 14px rgba(15, 118, 110, 0.25);
  }

  .quick-category-card {
    margin-top: 0.85rem;
    padding: 0.75rem 0.8rem;
  }

  .quick-category-btn {
    border: 1px solid #d7e2ee;
    background: #fff;
    color: #243b53;
    border-radius: 12px;
    font-weight: 800;
    font-size: 0.76rem;
    padding: 0.42rem 0.25rem;
    text-align: center;
    transition: all 0.18s ease;
  }

  .quick-category-btn.active {
    background: linear-gradient(135deg, rgba(15, 118, 110, 0.14) 0%, rgba(21, 158, 144, 0.16) 100%);
    border-color: rgba(15, 118, 110, 0.38);
    color: #0b5f58;
    box-shadow: 0 8px 14px rgba(15, 118, 110, 0.12);
  }

  .quick-letter-btn,
  .recent-candidate-chip {
    border: 1px solid #d7e2ee;
    background: #fff;
    color: #243b53;
    border-radius: 999px;
    font-weight: 800;
    font-size: 0.78rem;
    padding: 0.28rem 0.72rem;
    white-space: nowrap;
  }

  .quick-letter-btn.active {
    background: rgba(15, 118, 110, 0.12);
    border-color: rgba(15, 118, 110, 0.34);
    color: var(--sp-primary-dark);
  }

  .recent-candidate-chip {
    background: #f8fcff;
  }

  .quick-target {
    box-shadow: 0 0 0 2px rgba(15, 118, 110, 0.32) !important;
    animation: spPulseQuick 0.4s ease;
  }

  .paper-floating-controls {
    position: fixed;
    left: 16px;
    bottom: 16px;
    z-index: 1090;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .paper-floating-btn {
    border: 0;
    border-radius: 999px;
    min-height: 42px;
    min-width: 138px;
    padding: 0.45rem 0.9rem;
    color: #fff;
    font-weight: 800;
    font-size: 0.83rem;
    box-shadow: 0 12px 22px rgba(16, 42, 67, 0.22);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
  }

  .paper-floating-btn--start {
    background: linear-gradient(135deg, #0f766e 0%, #159e90 100%);
  }

  .paper-floating-btn--report {
    background: linear-gradient(135deg, #1d4ed8 0%, #1e64f0 100%);
  }

  .paper-report-list {
    max-height: 58vh;
    overflow-y: auto;
    padding: 0.25rem 0.25rem 0.15rem;
    display: grid;
    gap: 0.8rem;
    background:
      linear-gradient(180deg, rgba(14, 165, 233, 0.05) 0%, rgba(15, 118, 110, 0.04) 100%),
      repeating-linear-gradient(0deg, rgba(148, 163, 184, 0.08), rgba(148, 163, 184, 0.08) 1px, transparent 1px, transparent 32px);
    border: 1px solid #dbe8f4;
    border-radius: 14px;
  }

  .paper-report-card {
    position: relative;
    overflow: hidden;
    border: 1px solid #cfdeec;
    border-radius: 15px;
    padding: 0.9rem 0.85rem 0.75rem;
    background:
      linear-gradient(180deg, rgba(255, 255, 255, 0.97) 0%, #fdfefe 100%);
    box-shadow: 0 12px 22px rgba(16, 42, 67, 0.1);
  }

  .paper-report-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 0;
    height: 0;
    border-top: 18px solid #dcecf8;
    border-right: 18px solid transparent;
  }

  .paper-report-card::after {
    content: "انتخاب";
    position: absolute;
    top: 9px;
    right: 10px;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    border: 2px dashed rgba(220, 38, 38, 0.38);
    color: rgba(185, 28, 28, 0.74);
    font-size: 0.66rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transform: rotate(-12deg);
    pointer-events: none;
  }

  .paper-report-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.52rem;
    gap: 0.6rem;
  }

  .paper-report-title-wrap {
    display: flex;
    flex-direction: column;
    gap: 0.08rem;
    min-width: 0;
  }

  .paper-report-paper-title {
    display: inline-flex;
    align-items: center;
    gap: 0.36rem;
    color: #123c67;
    font-weight: 900;
    font-size: 0.92rem;
    line-height: 1.2;
  }

  .paper-report-paper-title i {
    color: #0f766e;
    font-size: 0.88rem;
  }

  .paper-report-meta {
    color: #5f7489;
    font-size: 0.72rem;
    font-weight: 700;
  }

  .paper-report-total-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    padding: 0.24rem 0.62rem;
    background: rgba(29, 78, 216, 0.12);
    border: 1px solid rgba(29, 78, 216, 0.26);
    color: #1d4ed8;
    font-size: 0.77rem;
    font-weight: 800;
    white-space: nowrap;
  }

  .paper-report-items {
    border-top: 1px dashed #d4e2ef;
    padding-top: 0.35rem;
  }

  .paper-report-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.6rem;
    font-size: 0.83rem;
    color: #334e68;
    padding: 0.37rem 0;
    border-bottom: 1px dashed #e5edf6;
  }

  .paper-report-item:last-child {
    border-bottom: 0;
  }

  .paper-report-item-name {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    flex-wrap: wrap;
  }

  .paper-report-action-tag {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 0.06rem 0.48rem;
    border: 1px solid rgba(15, 118, 110, 0.32);
    background: rgba(15, 118, 110, 0.1);
    color: #0b5f58;
    font-size: 0.7rem;
    font-weight: 800;
    white-space: nowrap;
  }

  .paper-report-votes {
    min-width: 68px;
    text-align: center;
    border-radius: 999px;
    padding: 0.11rem 0.58rem;
    background: rgba(15, 118, 110, 0.11);
    border: 1px solid rgba(15, 118, 110, 0.24);
    color: #0b5f58;
    font-size: 0.79rem;
    font-weight: 900;
    line-height: 1.25;
  }

  .paper-report-item--empty {
    justify-content: center;
    color: #5f7489;
    font-weight: 700;
    border-bottom: 0;
  }

  .paper-report-modal-close {
    --bs-btn-close-bg: none;
    opacity: 1;
    filter: none !important;
    background: #ffffff !important;
    background-image: none !important;
    border: 1px solid #cbd9e8;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(16, 42, 67, 0.12);
    width: 1.95rem;
    height: 1.95rem;
    padding: 0.42rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .paper-report-modal-close::before {
    content: "\00d7";
    color: #102a43;
    font-size: 1.16rem;
    font-weight: 800;
    line-height: 1;
  }

  .paper-report-modal-close:hover,
  .paper-report-modal-close:focus {
    opacity: 1;
    background-color: #eef4fb;
    border-color: #9ab4d0;
    box-shadow: 0 6px 14px rgba(16, 42, 67, 0.18);
  }

  .paper-report-reset-btn {
    border: 1px solid rgba(220, 38, 38, 0.34);
    background: rgba(220, 38, 38, 0.1);
    color: #b91c1c;
    border-radius: 999px;
    min-height: 34px;
    padding: 0.3rem 0.72rem;
    font-size: 0.77rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.34rem;
    white-space: nowrap;
    transition: all 0.18s ease;
  }

  .paper-report-reset-btn:hover,
  .paper-report-reset-btn:focus {
    color: #991b1b;
    border-color: rgba(185, 28, 28, 0.45);
    background: rgba(220, 38, 38, 0.16);
  }

  .paper-report-empty {
    padding: 1.3rem 1rem;
    text-align: center;
    color: #486581;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.78);
    border: 1px dashed #c5d5e6;
    border-radius: 12px;
  }

  .committee-stats {
    margin-top: 1rem;
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.8rem;
  }

  .stat-item {
    border-radius: 14px;
    border: 1px solid #d7e6e9;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    padding: 0.8rem;
    text-align: center;
    transition: transform 0.24s ease, box-shadow 0.24s ease;
    animation: spFadeUp 0.45s ease;
  }

  .stat-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(16, 42, 67, 0.1);
  }

  .stat-title {
    margin: 0;
    color: var(--sp-muted);
    font-size: 0.78rem;
    font-weight: 700;
  }

  .stat-value {
    margin: 0.35rem 0 0;
    color: var(--sp-ink);
    font-weight: 800;
    font-size: 1rem;
  }

  .sorting-control-row {
    margin-top: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.8rem;
    flex-wrap: wrap;
  }

  .bulk-vote-card {
    margin-top: 0.9rem;
    padding: 0.9rem 1rem;
    border: 1px solid #d7e2ee;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, #f7fbff 100%);
  }

  .bulk-vote-card--sticky {
    position: sticky;
    top: var(--sorting-bulk-sticky-top);
    z-index: 1040;
    box-shadow: 0 14px 26px rgba(16, 42, 67, 0.16);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
  }

  .bulk-vote-row {
    display: grid;
    grid-template-columns: minmax(130px, 180px) minmax(140px, 170px) minmax(160px, 220px) minmax(120px, 150px) minmax(100px, 1fr);
    gap: 0.6rem;
    align-items: center;
  }

  .bulk-vote-meta {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    color: #334e68;
    font-size: 0.84rem;
    font-weight: 800;
    white-space: nowrap;
  }

  .bulk-vote-meta strong {
    color: #0f766e;
  }

  .btn-bulk-vote,
  .btn-clear-selection {
    border: 0;
    border-radius: 11px;
    min-height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    font-size: 0.83rem;
    font-weight: 800;
    transition: transform 0.16s ease, filter 0.16s ease;
  }

  .btn-bulk-vote {
    color: #fff;
    background: linear-gradient(135deg, #1f9d66 0%, #26b26f 100%);
  }

  .btn-bulk-vote:disabled {
    cursor: not-allowed;
    opacity: 0.65;
    filter: grayscale(0.2);
  }

  .btn-clear-selection {
    color: #fff;
    background: linear-gradient(135deg, #64748b 0%, #475569 100%);
  }

  .select-candidate-checkbox,
  .select-all-candidates {
    width: 18px;
    height: 18px;
    accent-color: #0f766e;
    cursor: pointer;
  }

  .select-col {
    width: 44px;
  }

  tr.row-selected {
    outline: 2px solid rgba(15, 118, 110, 0.25);
  }

  .sorting-status {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    border-radius: 12px;
    background: #f6f9fc;
    border: 1px solid #d7e2ee;
    color: #334e68;
    padding: 0.55rem 0.85rem;
    font-size: 0.85rem;
    font-weight: 700;
  }

  .sorting-status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--sp-success);
    box-shadow: 0 0 0 0 rgba(31, 157, 102, 0.4);
    animation: spPulse 1.6s infinite;
  }

  .btn-lock-control {
    border: 0;
    border-radius: 13px;
    background: linear-gradient(135deg, var(--sp-primary) 0%, #159e90 100%);
    color: #fff;
    min-width: 190px;
    height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-weight: 800;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .btn-lock-control:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 18px rgba(15, 118, 110, 0.32);
  }

  .candidates-card {
    margin-top: 1rem;
    position: relative;
    overflow: hidden;
    animation: spFadeUp 0.55s ease;
  }

  .candidates-head {
    padding: 0.9rem 1rem;
    border-bottom: 1px solid #d7e2ee;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.65rem;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.95), #f8fcff);
  }

  .candidates-head h4 {
    margin: 0;
    font-size: 1rem;
    font-weight: 800;
    color: var(--sp-ink);
  }

  .table-wrap {
    padding: 0 0.6rem 0.7rem;
  }

  #candidates_table,
  #lists_table {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0 0.56rem;
  }

  #candidates_table thead th,
  #lists_table thead th {
    border: 0;
    color: #486581;
    font-size: 0.78rem;
    font-weight: 800;
    text-transform: uppercase;
    background: transparent;
    letter-spacing: 0.03em;
    padding: 0.45rem;
  }

  #candidates_table tbody tr,
  #lists_table tbody tr {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 8px 16px rgba(16, 42, 67, 0.08);
    transition: transform 0.22s ease, box-shadow 0.22s ease;
    animation: spFadeUp 0.35s ease;
    animation-delay: calc(var(--row-index, 0) * 40ms);
  }

  #candidates_table tbody tr:hover,
  #lists_table tbody tr:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 22px rgba(16, 42, 67, 0.12);
  }

  #candidates_table tbody tr.candidate-row-list,
  #lists_table tbody tr.candidate-row-list {
    background: linear-gradient(135deg, #fff8eb 0%, #fff2d8 100%);
    box-shadow: 0 8px 16px rgba(158, 99, 0, 0.14);
  }

  #candidates_table tbody td,
  #lists_table tbody td {
    border: 0;
    vertical-align: middle;
    padding: 0.72rem 0.45rem;
    font-size: 0.92rem;
    font-weight: 700;
    color: #243b53;
  }

  .candidate-name {
    color: #102a43;
    font-weight: 800;
  }

  .candidate-name-desktop {
    display: inline;
  }

  .candidate-mobile-line {
    display: none;
  }

  .candidate-mobile-rank {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 28px;
    border-radius: 8px;
    background: #eef4fb;
    color: #334e68;
    font-size: 0.78rem;
    font-weight: 800;
  }

  .mobile-vote-pill {
    min-width: 52px;
    padding: 0.22rem 0.62rem;
    font-size: 0.82rem;
    font-weight: 900;
    color: #fff;
    background: linear-gradient(135deg, #0f766e 0%, #159e90 100%);
    border: 1px solid #0b5f58;
    box-shadow: 0 6px 12px rgba(15, 118, 110, 0.28);
  }

  .list-pill {
    display: inline-flex;
    align-items: center;
    margin-inline-start: 0.4rem;
    padding: 0.14rem 0.5rem;
    border-radius: 999px;
    background: rgba(158, 99, 0, 0.14);
    color: #7a4b00;
    font-size: 0.72rem;
    font-weight: 800;
    white-space: nowrap;
  }

  .vote-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 58px;
    border-radius: 999px;
    padding: 0.28rem 0.7rem;
    background: rgba(15, 118, 110, 0.12);
    color: var(--sp-primary-dark);
    font-weight: 800;
    transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
    transform-origin: center;
  }

  .vote-pill.updated {
    animation: spVotePop 0.52s cubic-bezier(0.2, 0.8, 0.2, 1);
  }

  .action-btn {
    border: 0;
    border-radius: 11px;
    width: 100%;
    min-height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    color: #fff;
    font-size: 0.82rem;
    font-weight: 800;
    transition: transform 0.16s ease, filter 0.16s ease;
  }

  .action-btn:hover {
    transform: translateY(-1px);
    filter: brightness(0.96);
  }

  .action-plus {
    background: linear-gradient(135deg, #1f9d66 0%, #26b26f 100%);
  }

  .action-minus {
    background: linear-gradient(135deg, #d64545 0%, #e35d5d 100%);
  }

  .action-set {
    background: linear-gradient(135deg, #1d4ed8 0%, #1e64f0 100%);
  }

  .empty-state {
    margin-top: 1rem;
    padding: 1.35rem;
    text-align: center;
    color: #334e68;
    animation: spFadeUp 0.45s ease;
  }

  .empty-state i {
    font-size: 2rem;
    color: #829ab1;
    margin-bottom: 0.55rem;
  }

  #lock-overlay {
    position: absolute;
    inset: 0;
    background: rgba(9, 30, 66, 0.78);
    display: none;
    z-index: 12;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    border-radius: 20px;
    text-align: center;
    padding: 1rem;
  }

  #lock-overlay .lock-icon,
  #lock-overlay .unlock-icon {
    font-size: 92px;
    color: #fff;
  }

  #lock-overlay .lock-icon {
    animation: lock-animation 1.35s ease-in-out infinite;
  }

  #lock-overlay .unlock-icon {
    display: none;
  }

  #lock-overlay .lock-text {
    margin-top: 0.85rem;
    color: #ffffff;
    font-weight: 800;
    font-size: 0.95rem;
  }

  @media (max-width: 991.98px) {
    .sorting-toolbar {
      grid-template-columns: 1fr;
    }

    .bulk-vote-row {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .committee-stats {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .bulk-vote-card--sticky {
      top: var(--sorting-bulk-sticky-top);
      z-index: 1045;
    }
  }

  @media (max-width: 767.98px) {
    .sorting-shell {
      display: flex;
      flex-direction: column;
    }

    .sorting-hero { order: 1; }
    .sorting-control-row { order: 2; }
    .sorting-toolbar--committee { order: 3; }
    .committee-stats { order: 4; }
    .mobile-quick-access { order: 5; }
    .sorting-toolbar--search { order: 6; }
    .bulk-vote-card--sticky { order: 7; }
    .candidates-card { order: 8; }

    .sorting-toolbar {
      position: static;
    }

    .bulk-vote-card--sticky {
      top: var(--sorting-bulk-sticky-top);
      z-index: 1050;
      margin-top: 0.6rem;
      border-radius: 14px;
    }

    .sorting-page {
      padding-top: 0.7rem;
    }

    .sorting-hero {
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
    }

    .sorting-subtitle {
      display: none;
    }

    .sorting-title {
      font-size: 1.02rem;
      line-height: 1.4;
    }

    .sorting-chip {
      font-size: 0.78rem;
    }

    .paper-report-list {
      max-height: 66vh;
      padding: 0.2rem;
    }

    .paper-report-card {
      padding: 0.78rem 0.7rem 0.68rem;
      border-radius: 13px;
    }

    .paper-report-card::after {
      width: 50px;
      height: 50px;
      font-size: 0.61rem;
      top: 8px;
      right: 8px;
    }

    .paper-report-head {
      flex-direction: column;
      align-items: flex-start;
    }

    .paper-report-total-pill {
      align-self: flex-end;
      font-size: 0.74rem;
      padding: 0.2rem 0.55rem;
    }

    .paper-report-item {
      gap: 0.5rem;
      font-size: 0.8rem;
    }

    .paper-report-votes {
      min-width: 58px;
      font-size: 0.75rem;
      padding: 0.1rem 0.48rem;
    }

    .committee-stats {
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 0.6rem;
    }

    .mobile-quick-access {
      display: block;
      margin-top: 0.8rem;
      padding: 0.8rem 0.85rem;
      border-radius: 14px;
      border: 1px solid #d7e2ee;
      background: rgba(255, 255, 255, 0.92);
      box-shadow: 0 8px 16px rgba(16, 42, 67, 0.09);
    }

    .mobile-quick-title {
      margin: 0 0 0.45rem;
      color: #486581;
      font-size: 0.8rem;
      font-weight: 800;
    }

    #candidates_table thead,
    #lists_table thead {
      display: none;
    }

    #candidates_table tbody tr,
    #lists_table tbody tr {
      display: flex;
      flex-wrap: wrap;
      gap: 0.2rem;
      margin-bottom: 0.7rem;
      padding: 0.55rem 0.6rem;
    }

    #candidates_table tbody td,
    #lists_table tbody td {
      padding: 0;
      min-height: 0;
      text-align: center;
    }

    #candidates_table tbody td::before,
    #lists_table tbody td::before {
      display: none;
    }

    #candidates_table tbody td:nth-child(1),
    #candidates_table tbody td:nth-child(2),
    #candidates_table tbody td:nth-child(6),
    #lists_table tbody td:nth-child(1),
    #lists_table tbody td:nth-child(2),
    #lists_table tbody td:nth-child(6) {
      display: none;
    }

    #candidates_table tbody td:nth-child(4),
    #lists_table tbody td:nth-child(4) {
      order: 1;
      flex: 0 0 100%;
      text-align: right;
      padding-bottom: 0.2rem;
    }

    #candidates_table tbody td:nth-child(1),
    #candidates_table tbody td:nth-child(3),
    #candidates_table tbody td:nth-child(5),
    #candidates_table tbody td:nth-child(7),
    #lists_table tbody td:nth-child(1),
    #lists_table tbody td:nth-child(3),
    #lists_table tbody td:nth-child(5),
    #lists_table tbody td:nth-child(7) {
      order: 2;
      display: flex;
      flex: 1 1 calc(25% - 0.14rem);
      min-width: 0;
    }

    .bulk-vote-row {
      grid-template-columns: 1fr;
    }

    .paper-floating-controls {
      left: 10px;
      right: 10px;
      bottom: 10px;
      flex-direction: row;
      gap: 0.45rem;
    }

    .paper-floating-btn {
      flex: 1 1 0;
      min-width: 0;
      padding: 0.45rem 0.6rem;
      font-size: 0.78rem;
    }

    .named-sort-grid {
      grid-template-columns: 1fr;
    }

    .btn-named-sort-save {
      width: 100%;
    }

    .candidate-name-desktop {
      display: none;
    }

    .candidate-mobile-line {
      display: flex;
      align-items: center;
      gap: 0.38rem;
      flex-wrap: wrap;
      position: relative;
      width: 100%;
      min-height: 30px;
      padding-left: 3.45rem;
    }

    .candidate-mobile-name {
      font-size: 1.03rem;
      font-weight: 800;
      color: #102a43;
    }

    .action-btn {
      justify-content: center;
      padding: 0.35rem 0.25rem;
      min-height: 36px;
      font-size: 0.76rem;
      width: 100%;
      border-radius: 9px;
    }

    .mobile-vote-pill {
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
    }

    .action-btn i {
      font-size: 0.72rem;
    }

    .sorting-control-row {
      flex-direction: column;
      align-items: stretch;
    }

    .btn-lock-control {
      width: 100%;
    }
  }

  @keyframes spSlideDown {
    from {
      transform: translateY(-8px);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }

  @keyframes spFadeUp {
    from {
      transform: translateY(10px);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }

  @keyframes spPulse {
    0% {
      box-shadow: 0 0 0 0 rgba(31, 157, 102, 0.35);
    }
    70% {
      box-shadow: 0 0 0 8px rgba(31, 157, 102, 0);
    }
    100% {
      box-shadow: 0 0 0 0 rgba(31, 157, 102, 0);
    }
  }

  @keyframes spPulseQuick {
    0% {
      transform: scale(1);
    }
    35% {
      transform: scale(1.08);
    }
    100% {
      transform: scale(1);
    }
  }

  @keyframes spVotePop {
    0% {
      transform: scale(1);
      box-shadow: 0 0 0 0 rgba(15, 118, 110, 0);
      filter: brightness(1);
    }
    28% {
      transform: scale(1.16);
      box-shadow: 0 0 0 8px rgba(15, 118, 110, 0.2);
      filter: brightness(1.06);
    }
    65% {
      transform: scale(0.95);
      box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.1);
      filter: brightness(0.99);
    }
    100% {
      transform: scale(1);
      box-shadow: 0 0 0 0 rgba(15, 118, 110, 0);
      filter: brightness(1);
    }
  }

  @keyframes lock-animation {
    0%,
    100% {
      transform: scale(1);
      opacity: 1;
    }

    50% {
      transform: scale(1.14);
      opacity: 0.76;
    }
  }

  @keyframes unlock-animation {
    0% {
      transform: scale(1);
      opacity: 1;
    }

    100% {
      transform: scale(0.82) rotate(90deg);
      opacity: 0;
    }
  }
</style>

<div class="sorting-page rtl" dir="rtl">
  <div class="container-fluid sorting-shell">
    <div class="sorting-card sorting-hero">
      <div>
        <h1 class="sorting-title">لوحة فرز الأصوات</h1>
        <p class="sorting-subtitle">واجهة فرز سريعة ومنظمة لتحديث أصوات المرشحين داخل كل لجنة.</p>
      </div>
      <div class="sorting-chip">
        <i class="fa-solid fa-chart-column"></i>
        <span>تحديث مباشر للنتائج</span>
      </div>
    </div>

    @if (auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('مرشح'))
      <div class="sorting-card sorting-toolbar sorting-toolbar--committee">
        <form action="{{ route('dashboard.sorting') }}" method="get" id="sorting-form" class="sorting-field">
          @csrf
          <label class="sorting-label" for="sorting-select">اختيار اللجنة</label>
          <select id="sorting-select" name="committee" class="sorting-select">
            <option value="" hidden>اختر اللجنة</option>
            @foreach ($committees as $i => $com)
              <option
                value="{{ $com->id }}"
                @if ($committee && $committee->id == $com->id) selected @endif>
                {{ $i + 1 }} - {{ $com->name }} ({{ $com->type }})
              </option>
            @endforeach
          </select>
        </form>
      </div>
    @endif

    @if ($candidates)
      <div class="committee-stats">
        <div class="stat-item">
          <p class="stat-title">اسم اللجنة</p>
          <p class="stat-value">{{ $committee->name }}</p>
        </div>
        <div class="stat-item">
          <p class="stat-title">نوع اللجنة</p>
          <p class="stat-value">{{ $committee->type }}</p>
        </div>
        <div class="stat-item">
          <p class="stat-title">إجمالي الحضور</p>
          <p class="stat-value"><span class="totalAttending">{{ $committee->voters()->count() }}</span></p>
        </div>
        <div class="stat-item">
          <p class="stat-title">مجموع الأصوات المفروزة</p>
          <p class="stat-value"><span class="totalSortingSound">{{ $committee->candidates->sum('pivot.votes') }}</span></p>
        </div>
      </div>

      <div class="sorting-control-row">
        <div class="sorting-status">
          <span class="sorting-status-dot"></span>
          <span id="sortingStatusText">حالة الفرز: نشط</span>
        </div>

        <form action="{{ route('dashboard.sorting.status') }}" method="POST" id="CommStatus">
          @csrf
          <input type="hidden" name="status" id="status" value="{{ (int) ($sortingStatus ?? 1) }}">
          <button id="toggle-lock-button" type="button" class="btn-lock-control">
            <i id="icon" class="fa-solid fa-unlock"></i>
            <span id="lockButtonText">تبديل حالة الفرز</span>
          </button>
        </form>
      </div>

      <div class="sorting-card quick-category-card">
        <div class="filter-mode-switch" id="filterModeSwitch">
          <button type="button" class="filter-mode-btn active" data-filter-mode="mode1">مود 1</button>
          <button type="button" class="filter-mode-btn" data-filter-mode="mode2">مود 2</button>
        </div>

        <div class="quick-category-filter" id="quickCategoryFilter">
          <button type="button" class="quick-category-btn" data-category="my_list">قائمتى</button>
          <button type="button" class="quick-category-btn" data-category="other_lists">القوائم الأخرى</button>
          <button type="button" class="quick-category-btn" data-category="independent">المستقلين</button>
        </div>
      </div>

      <div class="mobile-quick-access" id="mobileQuickAccess">
        <div class="recent-candidates mb-2">
          <p class="mobile-quick-title mb-1">آخر مرشحين تم التعامل معهم</p>
          <div class="recent-candidates-chips" id="recentCandidatesChips"></div>
        </div>
        <div class="quick-letters">
          <p class="mobile-quick-title mb-1">اختصار بالحرف</p>
          <div class="quick-letter-filter" id="quickLetterFilter"></div>
        </div>
      </div>

      <div class="sorting-card sorting-toolbar sorting-toolbar--search">
        <div class="sorting-field">
          <label class="sorting-label" for="searchBox">بحث باسم المرشح</label>
          <div class="search-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="اكتب اسم المرشح" class="sorting-input" name="candidateName" id="searchBox" value="">
          </div>
        </div>

        <div class="sorting-field">
          <label class="sorting-label" for="sortBySelect">ترتيب الجدول</label>
          <div class="sorting-sort-inline">
            <select id="sortBySelect" class="sorting-select">
              <option value="default">الافتراضي</option>
              <option value="order">الترتيب</option>
              <option value="name">الاسم</option>
              <option value="votes">الأصوات</option>
              <option value="named">ترتيب مختار</option>
            </select>

            <select id="sortDirectionSelect" class="sorting-select">
              <option value="asc">تصاعدي</option>
              <option value="desc">تنازلي</option>
            </select>
          </div>
        </div>

        <div class="named-sort-panel" id="namedSortPanel">
          <div class="named-sort-grid">
            <select id="namedSortPresetSelect" class="sorting-select">
              <option value="">ترتيب جديد</option>
            </select>

            <input type="text" id="namedSortNameInput" class="sorting-input" placeholder="اسم الترتيب (مثال: ترتيب اليوم الصباحي)">

            <button type="button" id="saveNamedSortButton" class="btn-named-sort-save">
              <i class="fa-solid fa-floppy-disk"></i>
              <span>حفظ الترتيب</span>
            </button>
          </div>

          <p class="named-sort-help">عند اختيار "ترتيب مختار" أدخل رقم كل مرشح ثم احفظ باسمك، وسيظهر مباشرة في قائمة الترتيبات.</p>

          <div class="named-sort-actions">
            <button type="button" id="autoFillOrderButton" class="btn-named-sort-aux">
              <i class="fa-solid fa-list-ol"></i>
              ترقيم تلقائي حسب العرض
            </button>

            <button type="button" id="toggleQuickAssignButton" class="btn-named-sort-aux">
              <i class="fa-solid fa-hand-pointer"></i>
              تفعيل الترقيم بالضغط
            </button>

            <button type="button" id="clearCustomOrderButton" class="btn-named-sort-aux">
              <i class="fa-solid fa-broom"></i>
              مسح الأرقام
            </button>

            <span id="quickAssignStatus" class="quick-assign-status">الوضع السريع: غير مفعل</span>
          </div>
        </div>
      </div>

      <div class="sorting-card bulk-vote-card bulk-vote-card--sticky">
        <div class="bulk-vote-row">
          <input type="number" min="0" class="sorting-input bulk-vote-value" placeholder="عدد الأصوات" value="1">

          <select class="sorting-select bulk-vote-action">
            <option value="increment">إضافة</option>
            <option value="decrement">إزالة</option>
            <option value="set">تحديد</option>
          </select>

          <button type="button" class="btn-bulk-vote bulk-vote-button" disabled>
            <i class="fa-solid fa-bolt"></i>
            <span>تنفيذ التصويت المجمع</span>
          </button>

          <button type="button" class="btn-clear-selection clear-selection-button">
            <i class="fa-solid fa-eraser"></i>
            <span>مسح التحديد</span>
          </button>

          <div class="bulk-vote-meta">
            عدد المحددين: <strong class="selected-candidates-count">0</strong>
          </div>
        </div>
      </div>

      <div class="sorting-card candidates-card">
        <div id="lock-overlay">
          <i class="fas fa-lock lock-icon"></i>
          <i class="fas fa-lock-open unlock-icon"></i>
          <p class="lock-text mb-0">تم إيقاف الفرز لهذه اللجنة حاليا</p>
        </div>

        <div class="candidates-head">
          <h4>قائمة المرشحين داخل اللجنة</h4>
          <span class="sorting-chip">عدد المرشحين: {{ count($candidates) }}</span>
        </div>

        @php
          $regularCandidates = collect($candidates)->filter(fn($candidate) => empty($candidate['is_list']))->values();
          $listCandidates = collect($candidates)->filter(fn($candidate) => !empty($candidate['is_list']))->values();
        @endphp

        <div class="table-wrap table-responsive">
          <table class="table rtl" id="candidates_table">
            <thead class="text-center">
              <tr>
                <th class="select-col">
                  <input type="checkbox" class="select-all-candidates" data-target-table="candidates_table" title="تحديد الكل">
                </th>
                <th>الترتيب</th>
                <th>إضافة</th>
                <th>المرشح</th>
                <th>إزالة</th>
                <th>الأصوات</th>
                <th>تحديد</th>
              </tr>
            </thead>

            <tbody class="text-center">
              @foreach ($regularCandidates as $index => $can)
                <tr class="{{ !empty($can['is_list']) ? 'candidate-row-list' : '' }}" style="--row-index: {{ $index }};" data-candidate-id="{{ $can['id'] }}" data-candidate-group="{{ $can['candidate_group'] ?? 'independent' }}" data-is-list="{{ !empty($can['is_list']) ? 1 : 0 }}" data-list-group-id="{{ (int) ($can['list_group_id'] ?? 0) }}" data-origin-table="candidates" data-origin-order="{{ $index }}">
                  <td data-label="تحديد">
                    <input type="checkbox" class="select-candidate-checkbox" value="{{ $can['id'] }}">
                  </td>
                  <td data-label="الترتيب">
                    <span class="order-display">{{ (int) ($can['sorting_order'] ?? 0) > 0 ? (int) $can['sorting_order'] : $index + 1 }}</span>
                    <input type="number" min="1" class="custom-order-input" value="{{ (int) ($can['sorting_order'] ?? 0) > 0 ? (int) $can['sorting_order'] : $index + 1 }}">
                  </td>
                  <td data-label="إضافة">
                    <button class="action-btn action-plus plusBtn sortBtn" data-message="{{ 'تأكيد إضافة صوت جديد للمرشح (' . $can['name'] . ')' }}">
                      <i class="fa-solid fa-plus"></i>
                      <span>إضافة</span>
                    </button>
                  </td>
                  <td class="candidate-name fullName" data-label="المرشح" data-candidate-name="{{ $can['name'] }}">
                    <span class="candidate-name-desktop">{{ $can['name'] }}</span>
                    <div class="candidate-mobile-line">
                      <span class="candidate-mobile-rank">{{ (int) ($can['sorting_order'] ?? 0) > 0 ? (int) $can['sorting_order'] : $index + 1 }}</span>
                      <span class="candidate-mobile-name">{{ $can['name'] }}</span>
                      <span id="mobile_vote_count_{{ $can['id'] }}" class="vote-pill mobile-vote-pill">{{ $can['votes'] }}</span>
                    </div>
                    @if (!empty($can['is_list']))
                      <span class="list-pill">قائمة</span>
                    @endif
                  </td>
                  <td data-label="إزالة">
                    <button class="action-btn action-minus minusBtn sortBtn" data-message="{{ 'تأكيد حذف صوت من المرشح (' . $can['name'] . ')' }}">
                      <i class="fa-solid fa-minus"></i>
                      <span>إزالة</span>
                    </button>
                  </td>
                  <td data-label="الأصوات">
                    <span id="vote_count_{{ $can['id'] }}" class="vote-pill">{{ $can['votes'] }}</span>
                  </td>
                  <td data-label="تحديد">
                    <button class="action-btn action-set setBtn sortBtn" data-message="{{ 'تأكيد تحديد عدد الأصوات للمرشح (' . $can['name'] . ')' }}">
                      <i class="fa-solid fa-pen-to-square"></i>
                      <span>تحديد</span>
                    </button>
                    <input type="hidden" class="row-committee" value="{{ $can['committee'] }}">
                    <input type="hidden" class="row-candidate-id" value="{{ $can['id'] }}">
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        @if ($listCandidates->isNotEmpty())
          <div id="listsSection">
          <div class="candidates-head mt-2">
            <h4>فرز القوائم</h4>
            <span class="sorting-chip">عدد القوائم: {{ $listCandidates->count() }}</span>
          </div>

          <div class="table-wrap table-responsive">
            <table class="table rtl" id="lists_table">
              <thead class="text-center">
                <tr>
                  <th class="select-col">
                    <input type="checkbox" class="select-all-candidates" data-target-table="lists_table" title="تحديد الكل">
                  </th>
                  <th>الترتيب</th>
                  <th>إضافة</th>
                  <th>القائمة</th>
                  <th>إزالة</th>
                  <th>الأصوات</th>
                  <th>تحديد</th>
                </tr>
              </thead>

              <tbody class="text-center">
                @foreach ($listCandidates as $index => $can)
                  <tr class="candidate-row-list" style="--row-index: {{ $index }};" data-candidate-id="{{ $can['id'] }}" data-candidate-group="{{ $can['candidate_group'] ?? 'other_lists' }}" data-is-list="1" data-list-group-id="{{ (int) ($can['list_group_id'] ?? $can['id']) }}" data-origin-table="lists" data-origin-order="{{ $index }}">
                    <td data-label="تحديد">
                      <input type="checkbox" class="select-candidate-checkbox" value="{{ $can['id'] }}">
                    </td>
                    <td data-label="الترتيب">
                      <span class="order-display">{{ (int) ($can['sorting_order'] ?? 0) > 0 ? (int) $can['sorting_order'] : $index + 1 }}</span>
                      <input type="number" min="1" class="custom-order-input" value="{{ (int) ($can['sorting_order'] ?? 0) > 0 ? (int) $can['sorting_order'] : $index + 1 }}">
                    </td>
                    <td data-label="إضافة">
                      <button class="action-btn action-plus plusBtn sortBtn" data-message="{{ 'تأكيد إضافة صوت جديد للمرشح (' . $can['name'] . ')' }}">
                        <i class="fa-solid fa-plus"></i>
                        <span>إضافة</span>
                      </button>
                    </td>
                    <td class="candidate-name fullName" data-label="القائمة" data-candidate-name="{{ $can['name'] }}">
                      <span class="candidate-name-desktop">{{ $can['name'] }}</span>
                      <div class="candidate-mobile-line">
                        <span class="candidate-mobile-rank">{{ (int) ($can['sorting_order'] ?? 0) > 0 ? (int) $can['sorting_order'] : $index + 1 }}</span>
                        <span class="candidate-mobile-name">{{ $can['name'] }}</span>
                        <span id="mobile_vote_count_{{ $can['id'] }}" class="vote-pill mobile-vote-pill">{{ $can['votes'] }}</span>
                      </div>
                      <span class="list-pill">قائمة</span>
                    </td>
                    <td data-label="إزالة">
                      <button class="action-btn action-minus minusBtn sortBtn" data-message="{{ 'تأكيد حذف صوت من المرشح (' . $can['name'] . ')' }}">
                        <i class="fa-solid fa-minus"></i>
                        <span>إزالة</span>
                      </button>
                    </td>
                    <td data-label="الأصوات">
                      <span id="vote_count_{{ $can['id'] }}" class="vote-pill">{{ $can['votes'] }}</span>
                    </td>
                    <td data-label="تحديد">
                      <button class="action-btn action-set setBtn sortBtn" data-message="{{ 'تأكيد تحديد عدد الأصوات للمرشح (' . $can['name'] . ')' }}">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>تحديد</span>
                      </button>
                      <input type="hidden" class="row-committee" value="{{ $can['committee'] }}">
                      <input type="hidden" class="row-candidate-id" value="{{ $can['id'] }}">
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          </div>
        @endif
      </div>

      <div class="paper-floating-controls">
        <button type="button" id="paperTrackerButton" class="paper-floating-btn paper-floating-btn--start">
          <i class="fa-solid fa-file-circle-plus"></i>
          <span>ابدأ</span>
        </button>

        <button type="button" id="paperReportButton" class="paper-floating-btn paper-floating-btn--report">
          <i class="fa-solid fa-clipboard-list"></i>
          <span>سجل الأوراق</span>
        </button>
      </div>

      @include('dashboard.sorting.model_pop_up')

      <div class="modal fade sorting-modal" id="paperReportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-header bg-light">
              <h5 class="modal-title text-dark d-inline-flex align-items-center" style="gap: .4rem;">
                <i class="fa-solid fa-file-signature text-primary"></i>
                <span>سجل التصويت بالأوراق</span>
              </h5>
              <div class="d-inline-flex align-items-center" style="gap: .45rem;">
                <button type="button" id="paperResetButton" class="paper-report-reset-btn">
                  <i class="fa-solid fa-rotate-left"></i>
                  <span>إعادة تعيين</span>
                </button>
                <button type="button" class="btn-close paper-report-modal-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
              </div>
            </div>
            <div class="modal-body">
              <div id="paperReportList" class="paper-report-list">
                <div class="paper-report-empty">جارى تحميل السجل...</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade sorting-modal" id="paperResetConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
          <div class="modal-content">
            <div class="modal-header bg-light">
              <h5 class="modal-title text-dark d-inline-flex align-items-center" style="gap: .35rem;">
                <i class="fa-solid fa-triangle-exclamation text-warning"></i>
                <span>تأكيد إعادة التعيين</span>
              </h5>
              <button type="button" class="btn-close paper-report-modal-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
              <p class="mb-2 fw-bold text-dark">سيتم حذف كل الأوراق المسجلة لهذه اللجنة.</p>
              <p class="mb-0 text-muted">بعد التأكيد ستعود الحالة إلى زر <strong>ابدأ</strong> من جديد. لا يمكن التراجع عن هذه العملية.</p>
            </div>
            <div class="modal-footer border-0 pt-0 d-flex justify-content-end" style="gap: .45rem;">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
              <button type="button" id="confirmPaperResetButton" class="btn btn-danger">
                <i class="fa-solid fa-trash-can ms-1"></i>
                تأكيد إعادة التعيين
              </button>
            </div>
          </div>
        </div>
      </div>
    @else
      <div class="sorting-card empty-state">
        <i class="fa-regular fa-folder-open"></i>
        <h5 class="mb-2">لا توجد بيانات فرز حاليا</h5>
        <p class="mb-0">اختر اللجنة أولا لعرض المرشحين وإدارة الأصوات بشكل مباشر.</p>
      </div>
    @endif
  </div>
</div>
@endsection

@push('js')
<script>
  $(document).ready(function() {
    var liveStatsUrl = @json(route('dashboard.sorting.live-stats'));
    var namedPresetsUrl = @json(route('dashboard.sorting.named-presets'));
    var namedPresetsSaveUrl = @json(route('dashboard.sorting.named-presets.save'));
    var paperCurrentUrl = @json(route('dashboard.sorting.papers.current'));
    var paperNextUrl = @json(route('dashboard.sorting.papers.next'));
    var paperLogEventUrl = @json(route('dashboard.sorting.papers.log-event'));
    var paperReportUrl = @json(route('dashboard.sorting.papers.report'));
    var paperResetUrl = @json(route('dashboard.sorting.papers.reset'));
    var realtimeChannels = [];
    var fallbackTimer = null;
    var liveStatsInFlight = false;
    var fallbackIntervalMs = 2000;
    var activeQuickLetter = '';
    var activeFilterMode = 'mode1';
    var activeCategory = 'all';
    var activeSortBy = 'default';
    var activeSortDirection = 'asc';
    var namedSortPresets = [];
    var currentPaperNumber = 0;
    var quickAssignMode = false;
    var quickAssignNext = 1;
    var recentCandidateIds = [];
    var maxRecentCandidates = 8;
    var bulkVoteInFlight = false;

    // Render modals at body level to avoid clipping by parent containers.
    if ($('#confirmModal').length) {
      $('#confirmModal').appendTo('body');
    }
    if ($('#successModal').length) {
      $('#successModal').appendTo('body');
    }
    if ($('#paperReportModal').length) {
      $('#paperReportModal').appendTo('body');
    }
    if ($('#paperResetConfirmModal').length) {
      $('#paperResetConfirmModal').appendTo('body');
    }

    var headerSelectors = [
      '.dashboard-topbar-desktop',
      '.dashboard-topbar-mobile',
      '.dashboard-topbar',
      '.page-main-header',
      '.main-header',
      '.app-header',
      '.layout-navbar',
      '.header',
      'header.fixed-top',
      '.navbar.fixed-top',
      '.topbar',
      '.horizontal-header'
    ];

    var sortingPrimaryHeader = null;
    var sortingHeaderHidden = false;
    var bulkStickySection = null;
    var sortingHeaderHideStartY = Number.POSITIVE_INFINITY;
    var sortingHeaderBaseHeight = 0;

    function resolvePrimaryFixedHeader() {
      var bestCandidate = null;
      var bestBottom = 0;

      headerSelectors.forEach(function(selector) {
        document.querySelectorAll(selector).forEach(function(el) {
          if (!el) {
            return;
          }

          var style = window.getComputedStyle(el);
          if (style.display === 'none' || style.visibility === 'hidden') {
            return;
          }

          if (!el.getClientRects || el.getClientRects().length === 0) {
            return;
          }

          if (style.position !== 'fixed' && style.position !== 'sticky') {
            return;
          }

          var rect = el.getBoundingClientRect();
          if (rect.bottom <= 0) {
            return;
          }

          if (rect.top > window.innerHeight * 0.35) {
            return;
          }

          if (rect.bottom > bestBottom) {
            bestBottom = rect.bottom;
            bestCandidate = el;
          }
        });
      });

      return bestCandidate;
    }

    function setSortingHeaderHidden(hidden) {
      sortingPrimaryHeader = sortingPrimaryHeader || resolvePrimaryFixedHeader();
      if (!sortingPrimaryHeader) {
        return;
      }

      if (hidden === sortingHeaderHidden) {
        return;
      }

      if (!sortingPrimaryHeader.dataset.sortingHeaderTransition) {
        sortingPrimaryHeader.dataset.sortingHeaderTransition = sortingPrimaryHeader.style.transition || '';
        sortingPrimaryHeader.dataset.sortingHeaderTransform = sortingPrimaryHeader.style.transform || '';
        sortingPrimaryHeader.dataset.sortingHeaderPointerEvents = sortingPrimaryHeader.style.pointerEvents || '';
      }

      sortingHeaderHidden = hidden;

      if (hidden) {
        sortingPrimaryHeader.style.transition = 'transform 0.22s ease, opacity 0.22s ease';
        sortingPrimaryHeader.style.transform = 'translateY(-110%)';
        sortingPrimaryHeader.style.pointerEvents = 'none';
      } else {
        sortingPrimaryHeader.style.transition = sortingPrimaryHeader.dataset.sortingHeaderTransition || '';
        sortingPrimaryHeader.style.transform = sortingPrimaryHeader.dataset.sortingHeaderTransform || '';
        sortingPrimaryHeader.style.pointerEvents = sortingPrimaryHeader.dataset.sortingHeaderPointerEvents || '';
      }
    }

    function updateSortingHeaderVisibility() {
      if (!sortingPrimaryHeader) {
        sortingPrimaryHeader = resolvePrimaryFixedHeader();
      }

      if (!sortingPrimaryHeader) {
        return;
      }

      if (!bulkStickySection) {
        bulkStickySection = document.querySelector('.bulk-vote-card--sticky');
      }

      if (!bulkStickySection) {
        setSortingHeaderHidden(false);
        return;
      }

      if (!Number.isFinite(sortingHeaderHideStartY)) {
        recalculateHeaderHideStartPoint();
      }

      var currentY = Math.max(0, window.scrollY || 0);
      if (currentY <= 8) {
        setSortingHeaderHidden(false);
        return;
      }

      if (currentY >= sortingHeaderHideStartY) {
        setSortingHeaderHidden(true);
      } else {
        setSortingHeaderHidden(false);
      }
    }

    function recalculateHeaderHideStartPoint() {
      sortingPrimaryHeader = resolvePrimaryFixedHeader();
      bulkStickySection = document.querySelector('.bulk-vote-card--sticky');

      if (!sortingPrimaryHeader || !bulkStickySection) {
        sortingHeaderHideStartY = Number.POSITIVE_INFINITY;
        return;
      }

      var headerRect = sortingPrimaryHeader.getBoundingClientRect();
      sortingHeaderBaseHeight = Math.max(0, Math.ceil(headerRect.height || 0));

      var bulkRect = bulkStickySection.getBoundingClientRect();
      var bulkTopInPage = Math.max(0, Math.floor((window.scrollY || 0) + bulkRect.top));

      // Hide header only when bulk section reaches the header band and is ready to take its place.
      sortingHeaderHideStartY = Math.max(0, bulkTopInPage - sortingHeaderBaseHeight - 8);
    }

    function getFixedHeaderOffset() {
      var maxBottom = 0;
      headerSelectors.forEach(function(selector) {
        document.querySelectorAll(selector).forEach(function(el) {
          if (!el) {
            return;
          }

          var style = window.getComputedStyle(el);
          if (style.display === 'none' || style.visibility === 'hidden') {
            return;
          }

          if (!el.getClientRects || el.getClientRects().length === 0) {
            return;
          }

          if (style.position !== 'fixed' && style.position !== 'sticky') {
            return;
          }

          var rect = el.getBoundingClientRect();
          if (rect.bottom <= 0) {
            return;
          }

          if (rect.top > window.innerHeight * 0.35) {
            return;
          }

          maxBottom = Math.max(maxBottom, rect.bottom);
        });
      });

      return Math.max(8, Math.ceil(maxBottom) + 6);
    }

    function applyBulkStickyTopOffset() {
      recalculateHeaderHideStartPoint();
      var offset = getFixedHeaderOffset();
      document.documentElement.style.setProperty('--sorting-bulk-sticky-top', offset + 'px');
    }

    var stickyOffsetTimer = null;
    function scheduleBulkStickyTopOffset() {
      if (stickyOffsetTimer) {
        clearTimeout(stickyOffsetTimer);
      }
      stickyOffsetTimer = setTimeout(applyBulkStickyTopOffset, 40);
    }

    applyBulkStickyTopOffset();
    setTimeout(applyBulkStickyTopOffset, 220);
    window.addEventListener('resize', scheduleBulkStickyTopOffset, { passive: true });
    window.addEventListener('orientationchange', scheduleBulkStickyTopOffset, { passive: true });
    window.addEventListener('scroll', function() {
      updateSortingHeaderVisibility();
      scheduleBulkStickyTopOffset();
    }, { passive: true });
    updateSortingHeaderVisibility();

    if ($('#sorting-select').length) {
      $('#sorting-select').on('change', function() {
        $('#sorting-form').submit();
      });
    }

    checkLocked();

    function normalizeCandidateName(name) {
      return String(name || '')
        .replace(/^القائمة:\s*/i, '')
        .trim();
    }

    function getCandidateNameFromRow($row) {
      var $nameCell = $row.find('.fullName').first();
      return normalizeCandidateName($nameCell.data('candidate-name') || $nameCell.text());
    }

    function getCandidateFirstLetter(name) {
      var normalized = normalizeCandidateName(name);
      return normalized ? normalized.charAt(0) : '';
    }

    function getRowByCandidateId(candidateId) {
      var numericId = parseInt(candidateId, 10) || 0;
      if (!numericId) {
        return $();
      }

      return $('tr[data-candidate-id="' + numericId + '"]').first();
    }

    function renderRecentCandidates() {
      var $container = $('#recentCandidatesChips');
      if (!$container.length) {
        return;
      }

      if (!recentCandidateIds.length) {
        $container.html('<span class="text-muted small">لا يوجد بعد</span>');
        return;
      }

      var html = recentCandidateIds.map(function(candidateId) {
        var $row = getRowByCandidateId(candidateId);
        if (!$row.length) {
          return '';
        }

        var candidateName = getCandidateNameFromRow($row);
        return '<button type="button" class="recent-candidate-chip" data-candidate-id="' + candidateId + '">' + candidateName + '</button>';
      }).join('');

      $container.html(html);
    }

    function trackRecentCandidate(candidateId) {
      var numericId = parseInt(candidateId, 10) || 0;
      if (!numericId) {
        return;
      }

      recentCandidateIds = recentCandidateIds.filter(function(id) {
        return id !== numericId;
      });
      recentCandidateIds.unshift(numericId);

      if (recentCandidateIds.length > maxRecentCandidates) {
        recentCandidateIds = recentCandidateIds.slice(0, maxRecentCandidates);
      }

      renderRecentCandidates();
    }

    function buildQuickLetterFilter() {
      var $container = $('#quickLetterFilter');
      if (!$container.length) {
        return;
      }

      var letters = [];
      $('#candidates_table tbody tr, #lists_table tbody tr').each(function() {
        var candidateName = getCandidateNameFromRow($(this));
        var letter = getCandidateFirstLetter(candidateName);
        if (letter) {
          letters.push(letter);
        }
      });

      letters = Array.from(new Set(letters)).sort(function(a, b) {
        return a.localeCompare(b, 'ar');
      });

      var html = '<button type="button" class="quick-letter-btn active" data-letter="">الكل</button>';
      letters.forEach(function(letter) {
        html += '<button type="button" class="quick-letter-btn" data-letter="' + letter + '">' + letter + '</button>';
      });

      $container.html(html);
    }

    function focusCandidateRow(candidateId) {
      var $row = getRowByCandidateId(candidateId);
      if (!$row.length) {
        return;
      }

      $row[0].scrollIntoView({
        behavior: 'smooth',
        block: 'center'
      });
      $row.addClass('quick-target');
      setTimeout(function() {
        $row.removeClass('quick-target');
      }, 650);
    }

    function sortRowsByOrigin($tbody) {
      var rows = $tbody.find('tr').get();
      rows.sort(function(a, b) {
        var aOrder = parseInt($(a).data('origin-order'), 10) || 0;
        var bOrder = parseInt($(b).data('origin-order'), 10) || 0;
        return aOrder - bOrder;
      });
      $tbody.append(rows);
    }

    function restoreRowsToOriginTable() {
      $('tr[data-origin-table]').each(function() {
        var $row = $(this);
        var originTable = String($row.data('origin-table') || '');
        if (originTable === 'lists') {
          $('#lists_table tbody').append($row);
        } else {
          $('#candidates_table tbody').append($row);
        }
      });

      sortRowsByOrigin($('#candidates_table tbody'));
      sortRowsByOrigin($('#lists_table tbody'));
    }

    function applyOtherListsOrder() {
      var $candidatesBody = $('#candidates_table tbody');
      var rows = $candidatesBody.find('tr').get();
      rows.sort(function(a, b) {
        var $a = $(a);
        var $b = $(b);
        var aGroup = parseInt($a.data('list-group-id'), 10) || 0;
        var bGroup = parseInt($b.data('list-group-id'), 10) || 0;
        if (aGroup !== bGroup) {
          return aGroup - bGroup;
        }

        var aIsList = String($a.data('is-list') || '0') === '1';
        var bIsList = String($b.data('is-list') || '0') === '1';
        if (aIsList !== bIsList) {
          return aIsList ? 1 : -1;
        }

        return getCandidateNameFromRow($a).localeCompare(getCandidateNameFromRow($b), 'ar');
      });

      $candidatesBody.append(rows);
    }

    function applyCategoryLayoutMode() {
      restoreRowsToOriginTable();
      $('#listsSection').show();

      if (activeFilterMode !== 'mode1') {
        return;
      }

      if (activeCategory !== 'other_lists') {
        return;
      }

      $('#lists_table tbody tr').appendTo('#candidates_table tbody');
      applyOtherListsOrder();
      $('#listsSection').hide();
    }

    function getFilterModeOptions(mode) {
      if (mode === 'mode2') {
        return [
          { key: 'list_members', label: 'التزام' },
          { key: 'all_candidates', label: 'مفرق' },
        ];
      }

      return [
        { key: 'my_list', label: 'قائمتى' },
        { key: 'other_lists', label: 'القوائم الأخرى' },
        { key: 'independent', label: 'المستقلين' },
      ];
    }

    function renderQuickCategoryButtons() {
      var $container = $('#quickCategoryFilter');
      if (!$container.length) {
        return;
      }

      var modeOptions = getFilterModeOptions(activeFilterMode);
      var html = modeOptions.map(function(item) {
        var isActive = activeCategory === item.key ? ' active' : '';
        return '<button type="button" class="quick-category-btn' + isActive + '" data-category="' + item.key + '">' + item.label + '</button>';
      }).join('');

      $container.html(html);
    }

    function rowMatchesActiveCategory($row) {
      var rowCategory = String($row.data('candidate-group') || 'independent');

      if (activeCategory === 'all') {
        return true;
      }

      if (activeFilterMode === 'mode2') {
        if (activeCategory === 'all_candidates') {
          return true;
        }

        if (activeCategory === 'list_members') {
          return rowCategory !== 'independent';
        }

        return true;
      }

      return rowCategory === activeCategory;
    }

    function getRowSortValue($row, sortBy) {
      if (sortBy === 'name') {
        return getCandidateNameFromRow($row).toLowerCase();
      }

      if (sortBy === 'named') {
        return getCustomOrderValue($row);
      }

      if (sortBy === 'votes') {
        var candidateId = parseInt($row.data('candidate-id'), 10) || 0;
        return parseInt($('#vote_count_' + candidateId).text(), 10) || 0;
      }

      if (sortBy === 'order') {
        return parseInt($row.find('.order-display').first().text(), 10) || 0;
      }

      return parseInt($row.data('origin-order'), 10) || 0;
    }

    function sortRowsInTbody($tbody) {
      var rows = $tbody.find('tr').get();
      rows.sort(function(a, b) {
        var $a = $(a);
        var $b = $(b);
        var aValue = getRowSortValue($a, activeSortBy);
        var bValue = getRowSortValue($b, activeSortBy);

        var compareResult = 0;
        if (activeSortBy === 'name') {
          compareResult = String(aValue).localeCompare(String(bValue), 'ar');
        } else {
          compareResult = (aValue - bValue);
        }

        if (compareResult === 0) {
          var aOrigin = parseInt($a.data('origin-order'), 10) || 0;
          var bOrigin = parseInt($b.data('origin-order'), 10) || 0;
          compareResult = aOrigin - bOrigin;
        }

        return activeSortDirection === 'desc' ? -compareResult : compareResult;
      });

      $tbody.append(rows);
    }

    function applyTableSort() {
      if (activeSortBy === 'default') {
        return;
      }

      if (activeCategory === 'other_lists') {
        sortRowsInTbody($('#candidates_table tbody'));
        return;
      }

      sortRowsInTbody($('#candidates_table tbody'));
      sortRowsInTbody($('#lists_table tbody'));
    }

    function getCurrentCommitteeId() {
      if ($('#sorting-select').length && $('#sorting-select').val()) {
        return parseInt($('#sorting-select').val(), 10) || 0;
      }

      var rowCommitteeValue = $('.row-committee').first().val();
      return parseInt(rowCommitteeValue, 10) || 0;
    }

    function filterModeStorageKey() {
      return 'sorting_filter_mode_v1_committee_' + (getCurrentCommitteeId() || 0);
    }

    function loadFilterModePreference() {
      try {
        var raw = localStorage.getItem(filterModeStorageKey());
        if (raw === 'mode1' || raw === 'mode2') {
          return raw;
        }
      } catch (error) {
        // no-op
      }

      return 'mode1';
    }

    function saveFilterModePreference() {
      localStorage.setItem(filterModeStorageKey(), activeFilterMode);
    }

    function escapeHtml(value) {
      return String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    function loadNamedSortPresets() {
      return axios.get(namedPresetsUrl, {
        params: {
          committee: getCurrentCommitteeId(),
        },
        headers: {
          'Accept': 'application/json',
        }
      }).then(function(response) {
        var data = response && response.data ? response.data : {};
        namedSortPresets = Array.isArray(data.presets) ? data.presets : [];
        return namedSortPresets;
      }).catch(function() {
        namedSortPresets = [];
        return [];
      });
    }

    function renderSortByOptions(selectedValue) {
      var $sortSelect = $('#sortBySelect');
      if (!$sortSelect.length) {
        return;
      }

      var desiredValue = String(selectedValue || $sortSelect.val() || 'default');

      var baseOptions = [
        '<option value="default">الافتراضي</option>',
        '<option value="order">الترتيب</option>',
        '<option value="name">الاسم</option>',
        '<option value="votes">الأصوات</option>'
      ];

      var presetOptions = namedSortPresets.map(function(preset) {
        return '<option value="preset:' + escapeHtml(preset.id) + '">' + escapeHtml(preset.name) + '</option>';
      });

      var html = baseOptions.concat(presetOptions).join('')
        + '<option value="named">ترتيب مختار</option>';

      $sortSelect.html(html);

      if ($sortSelect.find('option[value="' + desiredValue + '"]').length) {
        $sortSelect.val(desiredValue);
      } else {
        $sortSelect.val('default');
      }
    }

    function renderNamedSortPresetOptions(selectedId) {
      var $presetSelect = $('#namedSortPresetSelect');
      if (!$presetSelect.length) {
        renderSortByOptions(selectedId ? ('preset:' + selectedId) : null);
        return;
      }

      var html = '<option value="">ترتيب جديد</option>';
      namedSortPresets.forEach(function(preset) {
        html += '<option value="' + String(preset.id || '') + '">' + String(preset.name || 'بدون اسم') + '</option>';
      });

      $presetSelect.html(html);
      if (selectedId) {
        $presetSelect.val(String(selectedId));
      }

      renderSortByOptions(selectedId ? ('preset:' + selectedId) : null);
    }

    function sortPreferenceStorageKey() {
      return 'sorting_selected_sort_v1_committee_' + (getCurrentCommitteeId() || 0);
    }

    function loadSortPreference() {
      try {
        var raw = localStorage.getItem(sortPreferenceStorageKey());
        var parsed = raw ? JSON.parse(raw) : {};
        return {
          sortBy: String(parsed.sortBy || 'default'),
          sortDirection: String(parsed.sortDirection || 'asc'),
        };
      } catch (error) {
        return {
          sortBy: 'default',
          sortDirection: 'asc',
        };
      }
    }

    function saveSortPreference() {
      localStorage.setItem(sortPreferenceStorageKey(), JSON.stringify({
        sortBy: String($('#sortBySelect').val() || 'default'),
        sortDirection: String($('#sortDirectionSelect').val() || 'asc'),
      }));
    }

    function setCustomOrderMode(enabled) {
      if (enabled) {
        $('#namedSortPanel').stop(true, true).slideDown(140);
        $('.custom-order-input').show();
        $('.order-display').hide();
      } else {
        $('#namedSortPanel').stop(true, true).slideUp(140);
        $('.custom-order-input').hide();
        $('.order-display').show();
        quickAssignMode = false;
        updateQuickAssignUI();
      }
    }

    function updateQuickAssignUI() {
      var $toggleButton = $('#toggleQuickAssignButton');
      var $status = $('#quickAssignStatus');

      if (!$toggleButton.length || !$status.length) {
        return;
      }

      if (quickAssignMode) {
        $toggleButton.addClass('active');
        $status.text('الوضع السريع: مفعل (اضغط على أي صف لإعطائه الرقم التالي)');
      } else {
        $toggleButton.removeClass('active');
        $status.text('الوضع السريع: غير مفعل');
      }
    }

    function visibleRowsForRanking() {
      return $('#candidates_table tbody tr:visible, #lists_table tbody tr:visible');
    }

    function setRowOrderValue($row, value) {
      if (!$row || !$row.length) {
        return;
      }

      $row.find('.custom-order-input').first().val(value);
      $row.find('.order-display').first().text(value);
      $row.find('.candidate-mobile-rank').first().text(value);
    }

    function nextAvailableOrder(startFrom) {
      var used = new Set();
      visibleRowsForRanking().each(function() {
        var value = parseInt($(this).find('.custom-order-input').first().val(), 10);
        if (!isNaN(value) && value > 0) {
          used.add(value);
        }
      });

      var candidate = Math.max(1, parseInt(startFrom, 10) || 1);
      while (used.has(candidate)) {
        candidate += 1;
      }

      return candidate;
    }

    function recomputeQuickAssignNext() {
      quickAssignNext = nextAvailableOrder(1);
    }

    function getCustomOrderValue($row) {
      var value = parseInt($row.find('.custom-order-input').first().val(), 10);
      if (isNaN(value) || value <= 0) {
        return Number.MAX_SAFE_INTEGER;
      }
      return value;
    }

    function collectCustomOrders() {
      var orders = {};

      $('#candidates_table tbody tr, #lists_table tbody tr').each(function() {
        var $row = $(this);
        var candidateId = parseInt($row.data('candidate-id'), 10) || 0;
        if (!candidateId) {
          return;
        }

        var value = parseInt($row.find('.custom-order-input').first().val(), 10);
        if (!isNaN(value) && value > 0) {
          orders[candidateId] = value;
        }
      });

      return orders;
    }

    function applyCustomOrders(orderMap) {
      if (!orderMap || typeof orderMap !== 'object') {
        return;
      }

      $('#candidates_table tbody tr, #lists_table tbody tr').each(function() {
        var $row = $(this);
        var candidateId = parseInt($row.data('candidate-id'), 10) || 0;
        if (!candidateId || typeof orderMap[candidateId] === 'undefined') {
          return;
        }

        var value = parseInt(orderMap[candidateId], 10);
        if (isNaN(value) || value <= 0) {
          return;
        }

        $row.find('.custom-order-input').first().val(value);
        $row.find('.order-display').first().text(value);
        $row.find('.candidate-mobile-rank').first().text(value);
      });
    }

    function getVisibleCandidateIds() {
      return $('.row-candidate-id').map(function() {
        return parseInt($(this).val(), 10) || 0;
      }).get().filter(function(id) {
        return id > 0;
      });
    }

    function selectedRows() {
      return $('.select-candidate-checkbox:checked').closest('tr');
    }

    function updateSelectionState() {
      var selectedCount = selectedRows().length;
      $('.selected-candidates-count').text(selectedCount);
      $('.bulk-vote-button').prop('disabled', selectedCount === 0 || bulkVoteInFlight);

      $('tr').removeClass('row-selected');
      $('.select-candidate-checkbox:checked').closest('tr').addClass('row-selected');

      $('.select-all-candidates').each(function() {
        var $master = $(this);
        var targetTable = String($master.data('target-table') || '');
        if (!targetTable) {
          return;
        }

        var $tableRows = $('#' + targetTable + ' tbody tr');
        var totalRows = $tableRows.length;
        if (!totalRows) {
          $master.prop('checked', false);
          return;
        }

        var selectedInTable = $tableRows.find('.select-candidate-checkbox:checked').length;
        $master.prop('checked', selectedInTable === totalRows);
      });
    }

    function parseBackendError(xhr, fallbackMessage) {
      var backendMessage = null;

      if (xhr && xhr.responseJSON) {
        backendMessage = xhr.responseJSON.message || xhr.responseJSON.error || null;
      }

      return backendMessage || fallbackMessage;
    }

    function updateVoteVisual(candidateId, voteCount) {
      var $votePill = $('#vote_count_' + candidateId);
      var $mobileVotePill = $('#mobile_vote_count_' + candidateId);

      if (!$votePill.length) {
        return;
      }

      $votePill.text(voteCount);
      $votePill.addClass('updated');

      if ($mobileVotePill.length) {
        $mobileVotePill.text(voteCount);
        $mobileVotePill.addClass('updated');
      }

      setTimeout(function() {
        $votePill.removeClass('updated');
        if ($mobileVotePill.length) {
          $mobileVotePill.removeClass('updated');
        }
      }, 420);

      if (activeSortBy === 'votes' || activeSortBy === 'order') {
        applyTableSort();
      }
    }

    function setVoteRequest(candidate_id, count_status, vote_count, committee) {
      return $.ajax({
        url: '/candidates/setVotes',
        type: 'POST',
        data: JSON.stringify({
          _token: $('meta[name="csrf-token"]').attr('content'),
          candidate_id: candidate_id,
          count_status: count_status,
          vote_count: vote_count,
          committee: committee,
        }),
        contentType: 'application/json',
      });
    }

    function updatePaperTrackerButton() {
      var $button = $('#paperTrackerButton');
      if (!$button.length) {
        return;
      }

      var label = currentPaperNumber > 0 ? ('الورقة ' + currentPaperNumber) : 'ابدأ';
      $button.find('span').text(label);
    }

    function loadCurrentPaper() {
      var committeeId = getCurrentCommitteeId();
      if (!committeeId) {
        currentPaperNumber = 0;
        updatePaperTrackerButton();
        return;
      }

      axios.get(paperCurrentUrl, {
        params: {
          committee: committeeId,
        },
        headers: {
          'Accept': 'application/json',
        }
      }).then(function(response) {
        var paper = response?.data?.paper || null;
        currentPaperNumber = paper && paper.number ? parseInt(paper.number, 10) || 0 : 0;
        updatePaperTrackerButton();
      }).catch(function() {
        currentPaperNumber = 0;
        updatePaperTrackerButton();
      });
    }

    function startNextPaper() {
      var committeeId = getCurrentCommitteeId();
      if (!committeeId) {
        toastr.error('اختر لجنة أولا قبل بدء الورقة.');
        return;
      }

      axios.post(paperNextUrl, {
        committee: committeeId,
      }, {
        headers: {
          'Accept': 'application/json',
        }
      }).then(function(response) {
        var paper = response?.data?.paper || null;
        currentPaperNumber = paper && paper.number ? parseInt(paper.number, 10) || 0 : 0;
        updatePaperTrackerButton();
        toastr.success(response?.data?.message || 'تم بدء ورقة جديدة.');
      }).catch(function(error) {
        var message = error?.response?.data?.message || 'تعذر بدء الورقة الآن.';
        toastr.error(message);
      });
    }

    function logVotePaperEvent(candidateId, committeeId, actionType, actionValue, deltaVotes) {
      if (!currentPaperNumber || !candidateId || !committeeId) {
        return;
      }

      if (!deltaVotes) {
        return;
      }

      axios.post(paperLogEventUrl, {
        committee: committeeId,
        candidate_id: candidateId,
        action_type: actionType,
        action_value: actionValue,
        delta_votes: deltaVotes,
      }, {
        headers: {
          'Accept': 'application/json',
        }
      }).catch(function() {
        // This log is for analytics only, so keep failures silent.
      });
    }

    function renderPaperReportList(papers) {
      var $container = $('#paperReportList');
      if (!$container.length) {
        return;
      }

      if (!Array.isArray(papers) || !papers.length) {
        $container.html('<div class="paper-report-empty">لا توجد أوراق مسجلة حتى الآن.</div>');
        return;
      }

      var html = papers.map(function(paper) {
        var items = Array.isArray(paper.items) ? paper.items : [];
        var itemsHtml = '';

        if (!items.length) {
          itemsHtml = '<div class="paper-report-item paper-report-item--empty"><span>لا توجد أحداث مسجلة لهذه الورقة.</span></div>';
        } else {
          itemsHtml = items.map(function(item) {
            var votes = parseInt(item.votes, 10) || 0;
            var actionLabel = String(item.set_action_label || '').trim();
            var actionBadge = actionLabel
              ? '<span class="paper-report-action-tag">' + escapeHtml(actionLabel) + '</span>'
              : '';
            return '<div class="paper-report-item"><span class="paper-report-item-name">'
              + '<span>' + escapeHtml(item.candidate_name || 'مرشح غير معروف') + '</span>'
              + actionBadge
              + '</span><strong class="paper-report-votes">' + votes + '</strong></div>';
          }).join('');
        }

        var paperNumber = parseInt(paper.number, 10) || 0;
        var totalVotes = parseInt(paper.total_votes, 10) || 0;

        return '<div class="paper-report-card">'
          + '<div class="paper-report-head">'
          + '<div class="paper-report-title-wrap">'
          + '<span class="paper-report-paper-title"><i class="fa-solid fa-file-lines"></i>الورقة ' + paperNumber + '</span>'
          + '<span class="paper-report-meta">متابعة انتخابية حسب الورقة</span>'
          + '</div>'
          + '<span class="paper-report-total-pill">إجمالي: ' + totalVotes + '</span>'
          + '</div>'
          + '<div class="paper-report-items">' + itemsHtml + '</div>'
          + '</div>';
      }).join('');

      $container.html(html);
    }

    function openPaperReportModal() {
      var committeeId = getCurrentCommitteeId();
      if (!committeeId) {
        toastr.error('اختر لجنة أولا لعرض سجل الأوراق.');
        return;
      }

      $('#paperReportList').html('<div class="paper-report-empty">جارى تحميل السجل...</div>');
      $('#paperReportModal').modal('show');

      axios.get(paperReportUrl, {
        params: {
          committee: committeeId,
        },
        headers: {
          'Accept': 'application/json',
        }
      }).then(function(response) {
        renderPaperReportList(response?.data?.papers || []);
      }).catch(function() {
        $('#paperReportList').html('<div class="paper-report-empty">تعذر تحميل سجل الأوراق حاليا.</div>');
      });
    }

    function resetPaperTrackingForCurrentCommittee() {
      var committeeId = getCurrentCommitteeId();
      if (!committeeId) {
        toastr.error('اختر لجنة أولا لإعادة تعيين سجل الأوراق.');
        return;
      }

      var $confirmBtn = $('#confirmPaperResetButton');
      if ($confirmBtn.prop('disabled')) {
        return;
      }

      var originalHtml = $confirmBtn.html();
      $confirmBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin ms-1"></i>جارى التنفيذ...');

      axios.post(paperResetUrl, {
        committee: committeeId,
      }, {
        headers: {
          'Accept': 'application/json',
        }
      }).then(function(response) {
        $('#paperResetConfirmModal').modal('hide');
        currentPaperNumber = 0;
        updatePaperTrackerButton();
        renderPaperReportList([]);
        toastr.success(response?.data?.message || 'تمت إعادة تعيين سجل الأوراق.');
      }).catch(function(error) {
        var message = error?.response?.data?.message || 'تعذر إعادة تعيين سجل الأوراق حاليا.';
        toastr.error(message);
      }).finally(function() {
        $confirmBtn.prop('disabled', false).html(originalHtml);
      });
    }

    function applyLiveStats(data) {
      if (!data || data.success !== true) {
        return;
      }

      if (typeof data.total_attending !== 'undefined') {
        $('.totalAttending').text(data.total_attending);
      }

      if (typeof data.total_sorting_votes !== 'undefined') {
        $('.totalSortingSound').text(data.total_sorting_votes);
      }

      if (data.candidate_votes) {
        Object.keys(data.candidate_votes).forEach(function(candidateId) {
          var selector = '#vote_count_' + candidateId;
          var $votePill = $(selector);
          var $mobileVotePill = $('#mobile_vote_count_' + candidateId);
          if (!$votePill.length) {
            return;
          }

          var newValue = parseInt(data.candidate_votes[candidateId], 10) || 0;
          var oldValue = parseInt($votePill.text(), 10) || 0;
          if (newValue !== oldValue) {
            $votePill.text(newValue);
            $votePill.addClass('updated');
            if ($mobileVotePill.length) {
              $mobileVotePill.text(newValue);
              $mobileVotePill.addClass('updated');
            }
            setTimeout(function() {
              $votePill.removeClass('updated');
              if ($mobileVotePill.length) {
                $mobileVotePill.removeClass('updated');
              }
            }, 420);
          }
        });
      }

      if (typeof data.sorting_status !== 'undefined') {
        $('#status').val(normalizeStatusValue(data.sorting_status));
        checkLocked();
      }
    }

    function fetchLiveStats() {
      if (liveStatsInFlight) {
        return;
      }

      var committeeId = getCurrentCommitteeId();
      if (!committeeId) {
        return;
      }

      var candidateIds = getVisibleCandidateIds();
      if (!candidateIds.length) {
        return;
      }

      liveStatsInFlight = true;

      axios.get(liveStatsUrl, {
        params: {
          committee: committeeId,
          candidate_ids: candidateIds,
        },
        headers: {
          'Accept': 'application/json',
        }
      }).then(function(response) {
        applyLiveStats(response.data || {});
      }).catch(function() {
        // Silent fail to avoid noisy toast every few seconds.
      }).finally(function() {
        liveStatsInFlight = false;
      });
    }

    function startSortingRealtime() {
      if (!$('.row-candidate-id').length) {
        return;
      }

      fetchLiveStats();

      var committeeId = getCurrentCommitteeId();
      if (!committeeId) {
        return;
      }

      if (window.Echo && typeof window.Echo.channel === 'function') {
        var sortingChannelName = 'sorting.' + committeeId;
        var sortingChannel = window.Echo.channel(sortingChannelName);
        sortingChannel.listen('.sorting.realtime.updated', function () {
          fetchLiveStats();
        });
        realtimeChannels.push(sortingChannelName);

        var committeeChannelName = 'committee';
        var committeeChannel = window.Echo.channel(committeeChannelName);
        committeeChannel.listen('.event', function () {
          fetchLiveStats();
        });
        realtimeChannels.push(committeeChannelName);
      }

      // Safety net: periodic refresh in case websocket drops silently.
      if (fallbackTimer) {
        clearInterval(fallbackTimer);
      }

      fallbackTimer = setInterval(function() {
        if (!document.hidden) {
          fetchLiveStats();
        }
      }, fallbackIntervalMs);
    }

    startSortingRealtime();
    loadCurrentPaper();
    buildQuickLetterFilter();
    renderRecentCandidates();
    activeFilterMode = loadFilterModePreference();
    $('.filter-mode-btn').removeClass('active');
    $('.filter-mode-btn[data-filter-mode="' + activeFilterMode + '"]').addClass('active');
    renderQuickCategoryButtons();
    applyCategoryLayoutMode();
    applyTableSort();
    updateSelectionState();
    var initialSortPreference = loadSortPreference();
    if (initialSortPreference.sortDirection === 'asc' || initialSortPreference.sortDirection === 'desc') {
      $('#sortDirectionSelect').val(initialSortPreference.sortDirection);
      activeSortDirection = initialSortPreference.sortDirection;
    }
    renderSortByOptions('default');
    loadNamedSortPresets().then(function() {
      renderNamedSortPresetOptions('');

      var preferredSortBy = String(initialSortPreference.sortBy || 'default');
      if ($('#sortBySelect').find('option[value="' + preferredSortBy + '"]').length) {
        $('#sortBySelect').val(preferredSortBy).trigger('change');
      } else {
        $('#sortBySelect').val('default').trigger('change');
      }

      saveSortPreference();
    });
    setCustomOrderMode(false);
    updateQuickAssignUI();

    $('#paperTrackerButton').on('click', function() {
      startNextPaper();
    });

    $('#paperReportButton').on('click', function() {
      openPaperReportModal();
    });

    $('#paperResetButton').on('click', function() {
      var committeeId = getCurrentCommitteeId();
      if (!committeeId) {
        toastr.error('اختر لجنة أولا لإعادة تعيين سجل الأوراق.');
        return;
      }

      $('#paperResetConfirmModal').modal('show');
    });

    $('#confirmPaperResetButton').on('click', function() {
      resetPaperTrackingForCurrentCommittee();
    });

    $('#sortBySelect').on('change', function() {
      var selectedSortValue = String($(this).val() || 'default');

      if (selectedSortValue.indexOf('preset:') === 0) {
        var presetId = selectedSortValue.replace('preset:', '');
        var selectedPreset = namedSortPresets.find(function(preset) {
          return String(preset.id || '') === presetId;
        });

        if (selectedPreset) {
          activeSortBy = 'named';
          applyCustomOrders(selectedPreset.orders || {});
          $('#namedSortPresetSelect').val(presetId);
          $('#namedSortNameInput').val(selectedPreset.name || '');
          setCustomOrderMode(false);
        } else {
          activeSortBy = 'default';
          setCustomOrderMode(false);
        }
      } else {
        activeSortBy = selectedSortValue;
        setCustomOrderMode(activeSortBy === 'named');
      }

      searchTable();
      saveSortPreference();
    });

    $('#sortDirectionSelect').on('change', function() {
      activeSortDirection = String($(this).val() || 'asc');
      searchTable();
      saveSortPreference();
    });

    $(document).on('input', '.custom-order-input', function() {
      var $input = $(this);
      var value = parseInt($input.val(), 10);
      var $row = $input.closest('tr');

      if (!isNaN(value) && value > 0) {
        $row.find('.order-display').first().text(value);
        $row.find('.candidate-mobile-rank').first().text(value);
      }

      if (activeSortBy === 'named') {
        applyTableSort();
      }

      recomputeQuickAssignNext();
    });

    $('#autoFillOrderButton').on('click', function() {
      if (activeSortBy !== 'named') {
        $('#sortBySelect').val('named').trigger('change');
      }

      var index = 1;
      visibleRowsForRanking().each(function() {
        setRowOrderValue($(this), index);
        index += 1;
      });

      recomputeQuickAssignNext();
      searchTable();
      toastr.success('تم ترقيم المرشحين تلقائيا حسب العرض الحالي.');
    });

    $('#clearCustomOrderButton').on('click', function() {
      visibleRowsForRanking().each(function() {
        $(this).find('.custom-order-input').first().val('');
        $(this).find('.order-display').first().text('-');
      });

      recomputeQuickAssignNext();
      if (activeSortBy === 'named') {
        applyTableSort();
      }
      toastr.info('تم مسح أرقام الترتيب للصفوف المعروضة.');
    });

    $('#toggleQuickAssignButton').on('click', function() {
      if (activeSortBy !== 'named') {
        $('#sortBySelect').val('named').trigger('change');
      }

      quickAssignMode = !quickAssignMode;
      recomputeQuickAssignNext();
      updateQuickAssignUI();
    });

    $(document).on('click', '#candidates_table tbody tr, #lists_table tbody tr', function(event) {
      if (!quickAssignMode || activeSortBy !== 'named') {
        return;
      }

      if ($(event.target).closest('button, input, select, a, label').length) {
        return;
      }

      var $row = $(this);
      var assignValue = quickAssignNext;
      setRowOrderValue($row, assignValue);

      $row.addClass('quick-assign-target');
      setTimeout(function() {
        $row.removeClass('quick-assign-target');
      }, 280);

      quickAssignNext = nextAvailableOrder(assignValue + 1);
      applyTableSort();
    });

    $('#namedSortPresetSelect').on('change', function() {
      var selectedId = String($(this).val() || '');

      if (!selectedId) {
        $('#namedSortNameInput').val('');
        return;
      }

      var selectedPreset = namedSortPresets.find(function(preset) {
        return String(preset.id || '') === selectedId;
      });

      if (!selectedPreset) {
        return;
      }

      $('#namedSortNameInput').val(selectedPreset.name || '');
      applyCustomOrders(selectedPreset.orders || {});
      $('#sortBySelect').val('preset:' + selectedId).trigger('change');
    });

    $('#saveNamedSortButton').on('click', function() {
      if (activeSortBy !== 'named') {
        $('#sortBySelect').val('named').trigger('change');
      }

      var sortName = String($('#namedSortNameInput').val() || '').trim();
      if (!sortName) {
        errorMessageInModel('اكتب اسم الترتيب قبل الحفظ.');
        return;
      }

      var orders = collectCustomOrders();
      var totalRows = $('#candidates_table tbody tr, #lists_table tbody tr').length;
      var orderKeysCount = Object.keys(orders).length;

      if (orderKeysCount !== totalRows) {
        errorMessageInModel('لازم تكتب رقم ترتيب لكل مرشح قبل الحفظ.');
        return;
      }

      var orderValues = Object.values(orders);
      var uniqueOrderValues = Array.from(new Set(orderValues));
      if (uniqueOrderValues.length !== orderValues.length) {
        errorMessageInModel('أرقام الترتيب يجب أن تكون بدون تكرار.');
        return;
      }

      var selectedPresetId = String($('#namedSortPresetSelect').val() || '');

      axios.post(namedPresetsSaveUrl, {
        committee: getCurrentCommitteeId(),
        name: sortName,
        orders: orders,
        preset_id: selectedPresetId || null,
      }, {
        headers: {
          'Accept': 'application/json'
        }
      }).then(function(response) {
        var data = response && response.data ? response.data : {};
        namedSortPresets = Array.isArray(data.presets) ? data.presets : [];
        selectedPresetId = String(data.preset_id || selectedPresetId || '');
        renderNamedSortPresetOptions(selectedPresetId);
        if (selectedPresetId) {
          $('#sortBySelect').val('preset:' + selectedPresetId).trigger('change');
        }
        toastr.success(data.message || 'تم حفظ الترتيب وإتاحته لكل مستخدمي الحملة.');
      }).catch(function(error) {
        var backendMessage = error?.response?.data?.message || 'تعذر حفظ الترتيب الآن.';
        errorMessageInModel(backendMessage);
      });
    });

    $('.bulk-vote-value').on('input', function() {
      var currentValue = $(this).val();
      $('.bulk-vote-value').not(this).val(currentValue);
    });

    $('.bulk-vote-action').on('change', function() {
      var currentValue = $(this).val();
      $('.bulk-vote-action').not(this).val(currentValue);
    });

    $(document).on('change', '.select-candidate-checkbox', function() {
      updateSelectionState();
    });

    $(document).on('change', '.select-all-candidates', function() {
      var targetTable = String($(this).data('target-table') || '');
      if (!targetTable) {
        return;
      }

      var shouldCheck = $(this).is(':checked');
      $('#' + targetTable + ' tbody .select-candidate-checkbox').prop('checked', shouldCheck);
      updateSelectionState();
    });

    $(document).on('click', '.clear-selection-button', function() {
      $('.select-candidate-checkbox, .select-all-candidates').prop('checked', false);
      updateSelectionState();
    });

    $(document).on('click', '.quick-letter-btn', function() {
      $('.quick-letter-btn').removeClass('active');
      $(this).addClass('active');
      activeQuickLetter = String($(this).data('letter') || '');
      searchTable();
    });

    $(document).on('click', '.quick-category-btn', function() {
      var $button = $(this);
      var selectedCategory = String($button.data('category') || '');
      if (!selectedCategory) {
        return;
      }

      if (activeCategory === selectedCategory) {
        activeCategory = 'all';
      } else {
        activeCategory = selectedCategory;
      }

      renderQuickCategoryButtons();

      applyCategoryLayoutMode();
      searchTable();
    });

    $(document).on('click', '.filter-mode-btn', function() {
      var selectedMode = String($(this).data('filter-mode') || 'mode1');
      if (!selectedMode || activeFilterMode === selectedMode) {
        return;
      }

      activeFilterMode = selectedMode;
      activeCategory = 'all';

      $('.filter-mode-btn').removeClass('active');
      $(this).addClass('active');

      renderQuickCategoryButtons();
      applyCategoryLayoutMode();
      searchTable();
      saveFilterModePreference();
    });

    $(document).on('click', '.recent-candidate-chip', function() {
      var candidateId = parseInt($(this).data('candidate-id'), 10) || 0;
      if (!candidateId) {
        return;
      }
      focusCandidateRow(candidateId);
    });

    $('.sortBtn').on('click', function(event) {
      event.preventDefault();

      var $row = $(this).closest('tr');
      var message = $(this).data('message');
      var id = $row.find('.row-candidate-id').val();
      var committee = $row.find('.row-committee').val();
      var count_status = 'set';

      if ($(this).hasClass('plusBtn')) {
        count_status = 'increment';
      } else if ($(this).hasClass('minusBtn')) {
        count_status = 'decrement';
      }

      var currentVotes = parseInt($('#vote_count_' + id).text(), 10) || 0;
      var defaultVotes = count_status === 'set' ? currentVotes : 1;
      trackRecentCandidate(id);

      $('#confirmMessage').text(message);
      $('#candidateIdInput').val(id);
      $('#statusInput').val(count_status);
      $('#vote_count').val(defaultVotes);
      $('#confirmCommitteeInput').val(committee);
      $('#confirmModal').modal('show');
    });

    $('#confirmButton').click(function() {
      var candidate_id = $('#candidateIdInput').val();
      var count_status = $('#statusInput').val();
      var vote_count = $('#vote_count').val();
      var committee = $('#confirmCommitteeInput').val();
      var candidate_vote = parseInt($('#vote_count_' + candidate_id).text(), 10) || 0;

      $('#confirmModal').modal('hide');

      if (vote_count === '' || isNaN(parseInt(vote_count, 10)) || parseInt(vote_count, 10) < 0) {
        errorMessageInModel('يرجى إدخال عدد أصوات صحيح.');
        return;
      }

      if ((count_status === 'decrement') && (parseInt(vote_count, 10) > candidate_vote)) {
        errorMessageInModel('لا يمكن حذف عدد أصوات أكبر من العدد الموجود.');
        return;
      }

      setVote(candidate_id, count_status, vote_count, committee);
    });

    function setVote(candidate_id, count_status, vote_count, committee) {
      if (!candidate_id) {
        return;
      }

      var previousVotes = parseInt($('#vote_count_' + candidate_id).text(), 10) || 0;

      setVoteRequest(candidate_id, count_status, vote_count, committee)
        .done(function(data) {
          if ((data.vote_count) || (data.vote_count === 0)) {
            var nextVotes = parseInt(data.vote_count, 10) || 0;
            var deltaVotes = nextVotes - previousVotes;
            updateVoteVisual(candidate_id, data.vote_count);
            refreshTotalSortingVotes();
            logVotePaperEvent(
              parseInt(candidate_id, 10) || 0,
              parseInt(committee, 10) || getCurrentCommitteeId(),
              count_status,
              parseInt(vote_count, 10) || 0,
              deltaVotes
            );
          }
          trackRecentCandidate(candidate_id);
          sucessMessageInModel(data.message);
        })
        .fail(function(xhr) {
          errorMessageInModel(parseBackendError(xhr, 'حدث خطأ أثناء تحديث الأصوات.'));
        });
    }

    $(document).on('click', '.bulk-vote-button', function() {
      if (bulkVoteInFlight) {
        return;
      }

      if (isLocked) {
        errorMessageInModel('الفرز متوقف حاليا، لا يمكن تنفيذ تصويت مجمع.');
        return;
      }

      var $rows = selectedRows();
      if (!$rows.length) {
        errorMessageInModel('اختر مرشحا واحدا على الأقل لتنفيذ التصويت المجمع.');
        return;
      }

      var $currentBulkCard = $(this).closest('.bulk-vote-card');
      var voteCountRaw = $currentBulkCard.find('.bulk-vote-value').val();
      var voteCount = parseInt(voteCountRaw, 10);
      if (voteCountRaw === '' || isNaN(voteCount) || voteCount < 0) {
        errorMessageInModel('يرجى إدخال عدد أصوات صحيح للتصويت المجمع.');
        return;
      }

      var countStatus = String($currentBulkCard.find('.bulk-vote-action').val() || 'increment');
      var invalidRows = [];

      if (countStatus === 'decrement') {
        $rows.each(function() {
          var $row = $(this);
          var candidateId = parseInt($row.find('.row-candidate-id').val(), 10) || 0;
          var currentVotes = parseInt($('#vote_count_' + candidateId).text(), 10) || 0;
          if (voteCount > currentVotes) {
            invalidRows.push(getCandidateNameFromRow($row));
          }
        });
      }

      if (invalidRows.length) {
        errorMessageInModel('لا يمكن الحذف لبعض المرشحين لأن العدد المطلوب أكبر من أصواتهم الحالية.');
        return;
      }

      var $bulkButton = $(this);
      $('.bulk-vote-button').each(function() {
        var $btn = $(this);
        if (!$btn.data('original-html')) {
          $btn.data('original-html', $btn.html());
        }
      });

      bulkVoteInFlight = true;
      $('.bulk-vote-button').prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i><span>جارى التنفيذ...</span>');

      var totalRows = $rows.length;
      var successCount = 0;
      var failCount = 0;

      var requestPromises = [];

      $rows.each(function() {
        var $row = $(this);
        var candidateId = parseInt($row.find('.row-candidate-id').val(), 10) || 0;
        var committee = parseInt($row.find('.row-committee').val(), 10) || getCurrentCommitteeId();
        var previousVotes = parseInt($('#vote_count_' + candidateId).text(), 10) || 0;

        if (!candidateId || !committee) {
          failCount += 1;
          return;
        }

        var requestPromise = setVoteRequest(candidateId, countStatus, voteCount, committee)
          .then(function(data) {
            if ((data.vote_count) || (data.vote_count === 0)) {
              var nextVotes = parseInt(data.vote_count, 10) || 0;
              var deltaVotes = nextVotes - previousVotes;
              updateVoteVisual(candidateId, data.vote_count);
              trackRecentCandidate(candidateId);
              logVotePaperEvent(candidateId, committee, countStatus, voteCount, deltaVotes);
              successCount += 1;
            } else {
              failCount += 1;
            }
          })
          .catch(function() {
            failCount += 1;
          });

        requestPromises.push(requestPromise);
      });

      Promise.allSettled(requestPromises).then(function() {
        refreshTotalSortingVotes();

        if (successCount > 0 && failCount === 0) {
          toastr.success('تم تنفيذ التصويت المجمع بنجاح لعدد ' + successCount + ' مرشح.');
        } else if (successCount > 0 && failCount > 0) {
          toastr.warning('تم تنفيذ التصويت لعدد ' + successCount + ' مرشح، وفشل ' + failCount + ' من أصل ' + totalRows + '.');
        } else {
          errorMessageInModel('تعذر تنفيذ التصويت المجمع.');
        }
      }).finally(function() {
        bulkVoteInFlight = false;
        $('.bulk-vote-button').each(function() {
          var $btn = $(this);
          $btn.html($btn.data('original-html') || '<i class="fa-solid fa-bolt"></i><span>تنفيذ التصويت المجمع</span>');
        });
        updateSelectionState();
      });
    });

    function refreshTotalSortingVotes() {
      var total = 0;
      $('span[id^="vote_count_"]').each(function() {
        total += parseInt($(this).text(), 10) || 0;
      });
      $('.totalSortingSound').text(total);
    }

    function errorMessageInModel(msg) {
      $('#successMessage').text(msg);
      $('#successModal .modal-header').removeClass('bg-success').addClass('bg-danger');
      $('#successModal .fa-check-circle').removeClass('text-success').addClass('text-danger');
      $('#successModal').modal('show');
    }

    function sucessMessageInModel(msg) {
      $('#successModal').modal('hide');
      toastr.success(msg || 'تم التصويت بنجاح');
    }

    $('#toggle-lock-button').on('click', function(e) {
      e.preventDefault();

      var $form = $('#CommStatus');
      var $btn = $(this);
      if (!$form.length || $btn.prop('disabled')) {
        return;
      }

      $btn.prop('disabled', true);

      axios.post($form.attr('action'), {
        _token: $('meta[name="csrf-token"]').attr('content'),
        status: normalizeStatusValue($('#status').val()),
      }, {
        headers: {
          'Accept': 'application/json'
        }
      }).then((res) => {
        var normalizedStatus = normalizeStatusValue(res.data.status);
        $('#status').val(normalizedStatus);
        checkLocked();
      }).catch(error => {
        var backendError = null;
        if (error.response && error.response.data) {
          backendError = error.response.data.error || error.response.data.message || null;
          if (!backendError && error.response.data.errors) {
            backendError = Object.values(error.response.data.errors).flat().join(' ');
          }
        }
        toastr.error(backendError || 'حدث خطأ غير متوقع أثناء تغيير حالة الفرز.');
      }).finally(() => {
        $btn.prop('disabled', false);
      });
    });

    let timer;
    $('#searchBox').on('keyup', function() {
      clearTimeout(timer);
      timer = setTimeout(function() {
        searchTable();
      }, 300);
    });

    function searchTable() {
      applyCategoryLayoutMode();
      applyTableSort();
      let entireValue = $('#searchBox').val().toLowerCase();
      $('#candidates_table tbody tr, #lists_table tbody tr').each(function() {
        let rowText = getCandidateNameFromRow($(this)).toLowerCase();
        let rowFirstLetter = getCandidateFirstLetter(rowText);
        let matchEntire = entireValue === '';
        let matchLetter = activeQuickLetter === '' || rowFirstLetter === activeQuickLetter;
        let matchCategory = rowMatchesActiveCategory($(this));
        if (entireValue !== '') {
          matchEntire = rowText.indexOf(entireValue) > -1;
        }
        $(this).toggle(matchEntire && matchLetter && matchCategory);
      });
    }

    window.addEventListener('beforeunload', function() {
      setSortingHeaderHidden(false);

      if (window.Echo && typeof window.Echo.leave === 'function') {
        realtimeChannels.forEach(function (channelName) {
          window.Echo.leave(channelName);
        });
      }

      if (fallbackTimer) {
        clearInterval(fallbackTimer);
      }
    });

    document.addEventListener('visibilitychange', function () {
      if (!document.hidden) {
        fetchLiveStats();
      }
    });
  });

  const toggleButton = document.getElementById("toggle-lock-button");
  const lockOverlay = document.getElementById("lock-overlay");
  const lockIcon = lockOverlay ? lockOverlay.querySelector(".lock-icon") : null;
  const unlockIcon = lockOverlay ? lockOverlay.querySelector(".unlock-icon") : null;
  const statusText = document.getElementById('sortingStatusText');
  const lockButtonText = document.getElementById('lockButtonText');
  let icon = $('#icon');
  let isLocked = false;

  function normalizeStatusValue(value) {
    if (value === true || value === 'true' || value === 1 || value === '1') {
      return 1;
    }
    return 0;
  }

  function checkLocked() {
    const statusElement = $('#status');
    if (!statusElement.length) {
      return;
    }

    let status = normalizeStatusValue(statusElement.val());

    if (status == 1) {
      unlockPage();
    } else if (status == 0) {
      lockPage();
    }
  }

  function updateLockLabels(locked) {
    if (!statusText || !lockButtonText) {
      return;
    }

    statusText.textContent = locked ? 'حالة الفرز: مقفل' : 'حالة الفرز: نشط';
    lockButtonText.textContent = locked ? 'فتح الفرز' : 'إيقاف الفرز';
  }

  function lockPage() {
    isLocked = true;

    icon.addClass("fa-unlock");
    icon.removeClass("fa-lock");

    if (lockOverlay && lockIcon && unlockIcon) {
      lockOverlay.style.display = "flex";
      lockIcon.style.display = "block";
      lockIcon.style.animation = "lock-animation 1.35s ease-in-out infinite";
      unlockIcon.style.display = "none";
    }

    $('#status').val(1);
    updateLockLabels(true);
  }

  function unlockPage() {
    isLocked = false;
    icon.addClass("fa-lock");
    icon.removeClass("fa-unlock");
    $('#status').val(0);

    if (lockOverlay && lockIcon && unlockIcon) {
      lockIcon.style.animation = "none";
      lockIcon.style.display = "none";
      unlockIcon.style.display = "block";
      unlockIcon.style.animation = "unlock-animation 0.8s ease-in-out";
    }

    updateLockLabels(false);

    if (lockOverlay && unlockIcon) {
      setTimeout(() => {
        lockOverlay.style.display = "none";
        unlockIcon.style.animation = "none";
      }, 800);
    }
  }
</script>
@endpush
