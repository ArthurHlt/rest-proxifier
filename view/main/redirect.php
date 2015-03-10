<?php
header('Location: ' . $this->route('index'));
?>
<!DOCTYPE html>
<html lang="en">
<body>
<script type="text/javascript">
    document.location.href = "<?php echo $this->route('index'); ?>";
</script>
</body>
</html>