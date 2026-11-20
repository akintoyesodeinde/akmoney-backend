<link href="{{ asset('assets/global/css/iziToast_custom.css') }}" rel="stylesheet">
<link href="{{ asset('assets/global/css/iziToast.min.css') }}" rel="stylesheet">
<script src="{{ asset('assets/global/js/iziToast.min.js') }}"></script>
<style>
/* make sure wrapper is fixed top-right (highest priority) */
html body .iziToast-wrapper,
.iziToast-wrapper {
  position: fixed !important;
  top: 20px !important;
  right: 20px !important;
  left: auto !important;
  bottom: auto !important;
  transform: none !important;
  margin: 0 !important;
  width: auto !important;
  max-width: 380px !important;
  display: flex !important;
  justify-content: flex-end !important;
  pointer-events: none; /* let clicks pass through wrapper except children */
  z-index: 999999 !important;
}

/* ensure each toast remains compact & inline */
.iziToast {
  pointer-events: auto;
  box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
  border-radius: 10px !important;
  overflow: hidden !important;
  padding: 12px 14px !important;
  background: #fff !important;
  display: flex !important;
  align-items: center !important;
  gap: 12px !important;
  width: auto !important;
  max-width: 360px !important;
}

/* body layout inside toast */
.iziToast .iziToast-body,
.iziToast > .iziToast-body {
  display: flex !important;
  align-items: center !important;
  justify-content: flex-start !important;
  gap: 12px !important;
  padding: 0 !important;
}

/* icon box */
.iziToast .iziToast-icon {
  min-width: 42px !important;
  min-height: 42px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  border-radius: 8px !important;
  padding: 8px !important;
  font-size: 18px !important;
}

/* text column */
.iziToast .iziToast-texts,
.iziToast .iziToast-texts * {
  text-align: left !important;
}

.iziToast .iziToast-title {
  font-weight: 600 !important;
  color: #222 !important;
}

.iziToast .iziToast-message {
  color: #666 !important;
  margin: 0 !important;
}

/* close button sized and positioned */
.iziToast .iziToast-close {
  width: 36px !important;
  height: 36px !important;
  right: 6px !important;
  top: 6px !important;
}

/* progress bar slim */
.iziToast .iziToast-progressbar > div {
  height: 4px !important;
}

/* mobile safety: wrap if not enough room */
@media (max-width: 480px) {
  .iziToast-wrapper { left: 10px !important; right: 10px !important; top: 12px !important; }
  .iziToast { max-width: calc(100% - 40px) !important; }
}
</style>

<script>
    "use strict";
    const colors = {
        success: '#28c76f',
        error: '#eb2222',
        warning: '#ff9f43',
        info: '#1e9ff2',
    }

    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-times-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-exclamation-circle',
    }

    const notifications = @json(session('notify', []));
    const errors = @json(@$errors ? collect($errors->all())->unique() : []);


    const triggerToaster = (status, message) => {
        iziToast[status]({
            title: status.charAt(0).toUpperCase() + status.slice(1),
            message: message,
            position: "topRight",
            backgroundColor: '#fff',
            icon: icons[status],
            iconColor: colors[status],
            progressBarColor: colors[status],
            titleSize: '1rem',
            messageSize: '1rem',
            titleColor: '#474747',
            messageColor: '#a2a2a2',
            transitionIn: 'bounceInLeft',
        });
    }

    if (notifications.length) {
        notifications.forEach(element => {
            triggerToaster(element[0], element[1]);
        });
    }

    if (errors.length) {
        errors.forEach(error => {
            triggerToaster('error', error);
        });
    }

    function notify(status, message) {
        if (typeof message == 'string') {
            triggerToaster(status, message);
        } else {
            $.each(message, (i, val) => triggerToaster(status, val));
        }
    }
</script>
