<div class="title_weather_mstp">
   <script language="JavaScript">
      var h=(new Date()).getHours();
      if (h > 23 || h <7) document.write("<?php
         echo $good_night . $start[2];
         ?>");
      if (h > 6 && h < 12) document.write("<?php
         echo $good_morning . $start[2];
         ?>");
      if (h > 11 && h < 19) document.write("<?php
         echo $good_day . $start[2];
         ?>");
      if (h > 18 && h < 24) document. write("<?php
         echo $good_evening . $start[2];
         ?>"); 
   </script>
</div>
