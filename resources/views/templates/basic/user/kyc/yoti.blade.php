<script src="https://sdk.yoti.com/clientsdk.js"></script>

<div id="yoti-root"></div>

<script>
  window.YotiClientSDK.init({
    sdkId: "{{ $sdkId }}",
    sessionId: "{{ $sessionId }}",
    sessionToken: "{{ $sessionToken }}",
    containerId: "yoti-root",
    onCompletion: function () {
        window.location.href = "{{ route('user.home') }}";
    }
  });
</script>
