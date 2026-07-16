<div id="errorModal" class="modal-overlay">
    <div class="modal-box text-center">
        <img src="<?= $base_url; ?>/assets/img/icons/alert.gif" alt="alert" height="120">
        <div class="modal-body" id="modalMessage"></div>
        <div class="modal-footer">
            <button id="modalOk">Bağla</button>
        </div>
    </div>
</div>

<style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .modal-box {
        background: #fff;
        width: 400px;
        border-radius: 8px;
        overflow: hidden;
        animation: fadeIn 0.2s ease;
    }


    .modal-body {
        padding: 16px;
        color: #333;
    }

    .modal-footer {
        padding: 12px 16px;
        text-align: right;
    }

    .modal-footer button {
        padding: 6px 0;
        width: 100%;
        border: none;
        background: #c10037;
        color: #fff;
        border-radius: 4px;
        cursor: pointer;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>