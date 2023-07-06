<?php
header('HTTP/1.1 503 Service Temporarily Unavailable', true, 503);
header('Retry-After: 600');

$strings = [
    'en' => [
        'dir' => 'ltr',
        'tld' => 'io',
        'title' => 'Maintenance Mode',
        'ssshhh' => 'Shhh....',
        'website_in_maintenance_mode' => 'Do not disturb, the website is currently under maintenance.',
    ],
    'he' => [
        'dir' => 'rtl',
        'tld' => 'co.il',
        'title' => 'מצב תחזוקה',
        'ssshhh' => 'שששששששש....',
        'website_in_maintenance_mode' => 'לא להפריע. האתר במצב תחזוקה כעת.',
    ]
];

$langs = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? array_map(function($l) { return preg_replace('/^(.+?);.+?$/', '$1', $l); }, explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE'])) : ['en'];
$lang = in_array('he', $langs) || in_array('he-IL', $langs) ? 'he' : 'en';
$strings = $strings[$lang];
?>
<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width,initial-scale=0.86,maximum-scale=3,minimum-scale=0.86">
    <title><?php echo $strings['title']; ?></title>
    <link rel="icon" href="https://www.upress.<?php echo $strings['tld']; ?>/wp-content/uploads/2018/05/favicon.png">
    <link rel="apple-touch-icon-precomposed" href="https://www.upress.<?php echo $strings['tld']; ?>/wp-content/uploads/2018/05/favicon.png">
    <link rel="apple-touch-icon" href="https://www.upress.<?php echo $strings['tld']; ?>/wp-content/uploads/2018/05/apple-touch-icon.png">
    <meta name="robots" content="none">
    <link href="https://fonts.googleapis.com/css?family=Heebo" rel="stylesheet">
    <style>blockquote,body,dd,dl,dt,fieldset,figure,h1,h2,h3,h4,h5,h6,hr,html,iframe,legend,li,ol,p,pre,textarea,ul{margin:0;padding:0}h1,h2,h3,h4,h5,h6{font-size:100%;font-weight:400}ul{list-style:none}button,input,select,textarea{margin:0}html{box-sizing:border-box}*,:after,:before{box-sizing:inherit}audio,embed,iframe,img,object,video{height:auto;max-width:100%}iframe{border:0}table{border-collapse:collapse;border-spacing:0}td,th{padding:0;text-align:left}html{font-size:62.5%;min-width:300px}body,html{width:100%;box-sizing:border-box;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;text-rendering:optimizeLegibility}body{background:#f5f7fa;height:100%;font-size:1.6rem;font-family:Heebo,sans-serif;-ms-overflow-style:none;overflow:-moz-scrollbars-none;overflow:hidden}.grid-background{background-image:linear-gradient(90deg,#fff 1px,transparent 0),linear-gradient(#fff 1px,transparent 0);background-size:30px 30px;position:absolute;top:-70rem;left:-22rem;transform:skewY(25deg) skewX(-40.3deg) rotate(6deg);transform-origin:center center;pointer-events:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;border-radius:0 0 100vw 0;z-index:-1;border-radius:100vw/100vw;height:140rem;width:100%}svg#client-leave{min-width:1900px;max-width:1900px;top:0;transform:translate(-50%)}.content,svg#client-leave{position:absolute;left:50%}.content{top:50%;transform:translate(-50%,-50%);text-align:center;font-size:1.8rem;direction:<?php echo $strings['dir']; ?>;line-height:1.5;min-width:300px;max-width:600px;display:-ms-flexbox;display:flex;-ms-flex-pack:justify;justify-content:space-between;-ms-flex-direction:column;flex-direction:column;text-shadow:0 2px 1px rgba(0,0,0,.1);-ms-flex-align:center;align-items:center}.content #upress-robocat{width:120px;margin-bottom:20px}.content ::-moz-selection{background:#e7eef6}.content ::selection{background:#e7eef6}.content h1{font-size:2.3rem;color:#000}.content h2{margin:0 0 8px;color:#72737a;text-shadow:0 2px 1px rgba(0,0,0,.1)}.content footer{margin-top:40px;color:#020c15;font-size:1.6rem;max-width:225px;font-weight:400}@media screen and (orientation:landscape) and (max-height:450px){.content footer{display:none}}.content footer h5{margin:0 0 10px;font-size:1.9rem;text-decoration:underline}.content footer a{text-decoration:none;color:#08c;display:block;margin-top:5px;transition:color .3s}.content footer a:hover{color:#5db7e2;font-style:normal}</style>
</head>
<body>

<div class="grid-background"></div>
<svg id="client-leave" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1897.5 1101.8">
    <style>.st0{fill:#dce7f2;stroke:#004680;stroke-width:1.4;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10}.st1{fill:#fff;stroke:#004680;stroke-width:1.4;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10}.st2{fill:none;stroke:#004680;stroke-width:1.4;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10}.st3{fill:#fff}.st4{fill:#e1eaf2;stroke:#004680;stroke-width:1.4;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10}.st5{opacity:.1;fill:#457088}.st6{fill:#004680}.st7{fill:#dce7f2}.st8{fill:#dce7f2;stroke:#004680;stroke-width:1.1923;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10}.st9{fill:#8ad68a}.st10{fill:#a7e8a7}.st11{fill:none;stroke:#004680;stroke-width:1.1923;stroke-miterlimit:10}.st12{fill:#dce7f2;stroke:#004680;stroke-width:1.013;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10}.st13{fill:#c6f2c7}.st14{fill:none;stroke:#004680;stroke-width:1.0127;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10}.st15{fill:none;stroke:#004680;stroke-width:1.1923;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10}.st16{fill:none;stroke:none}.st17{fill-rule:evenodd;clip-rule:evenodd;fill:#151600}.st18{fill-rule:evenodd;clip-rule:evenodd;fill:#1f88ca}.st19{fill-rule:evenodd;clip-rule:evenodd;fill:#185f9e}.st20{fill-rule:evenodd;clip-rule:evenodd;fill:#26acef}.st21{fill-rule:evenodd;clip-rule:evenodd;fill:#fff}</style>
    <g id="trees_11_">
        <ellipse transform="matrix(5.419920e-03 -1 1 5.419920e-03 482.7352 2573.2361)" class="st7" cx="1535" cy="1043.9" rx="4.5" ry="7.9"/>
        <path class="st8" d="M1536,1044.6h-2.2c-0.4,0-0.8-0.3-0.8-0.8v-19.9c0-0.4,0.3-0.8,0.8-0.8h2.2c0.4,0,0.8,0.3,0.8,0.8v19.9
		C1536.7,1044.2,1536.4,1044.6,1536,1044.6L1536,1044.6z"/>
        <path class="st9" d="M1545.3,1018.1c0,6.3-5.1,11.3-11.3,11.3c-3.2,0-6.1-1.3-8.2-3.5c-1.9-2-3.2-4.8-3.2-7.9
		c0-6.3,5.1-11.3,11.3-11.3c4.9,0,9,3.1,10.6,7.5C1545.2,1015.5,1545.3,1016.8,1545.3,1018.1L1545.3,1018.1z"/>
        <path class="st10" d="M1544.7,1014.3c-0.9,7-6.9,12.4-14.2,12.4c-1.6,0-3.1-0.3-4.6-0.8c-1.9-2-3.2-4.8-3.2-7.9
		c0-6.3,5.1-11.3,11.3-11.3C1539,1006.8,1543.2,1009.9,1544.7,1014.3L1544.7,1014.3z"/>
        <path class="st11" d="M1545.3,1018.1c0,6.3-5.1,11.3-11.3,11.3c-3.2,0-6.1-1.3-8.2-3.5c-1.9-2-3.2-4.8-3.2-7.9
		c0-6.3,5.1-11.3,11.3-11.3c4.9,0,9,3.1,10.6,7.5C1545.2,1015.5,1545.3,1016.8,1545.3,1018.1L1545.3,1018.1z"/>
        <ellipse transform="matrix(0.3628 -0.9319 0.9319 0.3628 26.4444 2069.7471)" class="st3" cx="1526.7" cy="1015.5" rx="3.1" ry="1.8"/>
        <g>
            <ellipse transform="matrix(5.419920e-03 -1 1 5.419920e-03 463.81 2564.8376)" class="st7" cx="1521.3" cy="1049.3" rx="3.6" ry="6.3"/>
            <path class="st8" d="M1522,1049.8h-1.4c-0.4,0-0.8-0.3-0.8-0.8v-15.6c0-0.4,0.3-0.8,0.8-0.8h1.4c0.4,0,0.8,0.3,0.8,0.8v15.6
			C1522.7,1049.4,1522.4,1049.8,1522,1049.8z"/>
            <path class="st9" d="M1529.6,1028.7c0,4.9-4,9-9,9c-2.6,0-4.9-1.1-6.5-2.8c-1.6-1.6-2.5-3.9-2.5-6.2c0-4.9,4-9,9-9
			c3.9,0,7.2,2.5,8.5,5.9C1529.4,1026.7,1529.6,1027.7,1529.6,1028.7z"/>
            <path class="st10" d="M1529.1,1025.7c-0.8,5.6-5.5,9.9-11.4,9.9c-1.3,0-2.5-0.3-3.7-0.6c-1.6-1.6-2.5-3.9-2.5-6.2c0-4.9,4-9,9-9
			C1524.5,1019.7,1527.8,1022.2,1529.1,1025.7L1529.1,1025.7z"/>
            <path class="st11" d="M1529.6,1028.7c0,4.9-4,9-9,9c-2.6,0-4.9-1.1-6.5-2.8c-1.6-1.6-2.5-3.9-2.5-6.2c0-4.9,4-9,9-9
			c3.9,0,7.2,2.5,8.5,5.9C1529.4,1026.7,1529.6,1027.7,1529.6,1028.7z"/>
            <ellipse transform="matrix(0.3628 -0.9319 0.9319 0.3628 8.4365 2065.7224)" class="st3" cx="1514.7" cy="1026.7" rx="2.4" ry="1.5"/>
        </g>
    </g>
    <g id="tree_1_">
        <path class="st7" d="M380.9,339.6c-5.4,3.1-5.4,8.2,0,11.3s14.2,3.1,19.5,0s5.4-8.2,0-11.3C395.1,336.5,386.3,336.5,380.9,339.6z"/>
        <path class="st12" d="M390.5,344.7c-1.8,0-3.4-1.4-3.4-3.4v-14.4h6.6v14.4C393.9,343.2,392.4,344.7,390.5,344.7L390.5,344.7z"/>
        <path class="st13" d="M402.1,326.2l-12.2-45.3c-11.9,48.3-10.9,44.4-11.2,45.5C378.1,335,401.8,336.6,402.1,326.2z"/>
        <path class="st14" d="M386.9,293c0,0.1,0,0.2-0.1,0.4"/>
        <path class="st10" d="M402.1,326.2c-0.4,10.3-24,8.9-23.4,0.2v-0.1c7.1,2.8,19.1,1.4,18.4-6.1c-1.2-10.3-4.7-25.2-7.1-37.9
		c-1.8,7.4-3.5,13.4-4.9,18.3c1.3-5.3,2.9-11.8,4.8-19.6L402.1,326.2L402.1,326.2z"/>
        <path class="st14" d="M385.7,297.8l-0.5,2c-7,28.2-6.4,25.7-6.5,26.5c-0.6,8.6,23.2,10.1,23.4-0.2l-12.2-45.3
		c-0.8,3.4-1.6,6.5-2.3,9.4"/>
    </g>
    <g id="tree_2_">
        <path class="st7" d="M507.2,867.9c-4.5,2.6-4.5,6.8,0,9.4c4.5,2.6,11.8,2.6,16.2,0c4.4-2.6,4.5-6.8,0-9.4
		C519,865.3,511.7,865.3,507.2,867.9z"/>
        <path class="st12" d="M515.2,872.1c-1.5,0-2.8-1.2-2.8-2.8v-12h5.5v12C518,870.9,516.8,872.1,515.2,872.1L515.2,872.1z"/>
        <path class="st13" d="M524.9,856.7L514.7,819c-9.9,40.2-9.1,37-9.3,37.9C504.9,864.1,524.6,865.4,524.9,856.7z"/>
        <path class="st14" d="M512.2,829.1c0,0.1,0,0.2-0.1,0.3"/>
        <path class="st10" d="M524.9,856.7c-0.3,8.6-20,7.4-19.5,0.2v-0.1c5.9,2.3,15.9,1.2,15.3-5.1c-1-8.6-3.9-21-5.9-31.6
		c-1.5,6.2-2.9,11.2-4.1,15.2c1.1-4.4,2.4-9.8,4-16.3L524.9,856.7L524.9,856.7z"/>
        <path class="st14" d="M511.2,833.1l-0.4,1.7c-5.8,23.5-5.3,21.4-5.4,22.1c-0.5,7.2,19.3,8.4,19.5-0.2l-10.2-37.7
		c-0.7,2.8-1.3,5.4-1.9,7.8"/>
    </g>
    <g id="trees_4_">
        <path class="st7" d="M1540.9,700c-5.5,3.2-5.5,8.4,0,11.6c5.5,3.2,14.5,3.2,20.1,0c5.5-3.2,5.5-8.4,0-11.6
		C1555.4,696.7,1546.4,696.7,1540.9,700z"/>
        <path class="st8" d="M1551,705.3h-0.5c-1.8,0-3.2-1.4-3.2-3.2V687h6.8v15C1554.2,703.8,1552.8,705.3,1551,705.3z"/>
        <path class="st13" d="M1562.6,686.2l-12.6-46.6c-12.2,49.7-11.3,45.8-11.5,46.8C1537.9,695.3,1562.4,696.8,1562.6,686.2z"/>
        <path class="st10" d="M1562.6,686.2c-0.4,10.7-24.7,9.1-24.1,0.2v-0.1c7.3,2.9,19.6,1.4,18.9-6.2c-1.2-10.6-4.8-25.9-7.3-39.1
		c-1.9,7.6-3.6,13.8-5,18.9c1.3-5.5,3-12.1,4.9-20.2L1562.6,686.2L1562.6,686.2z"/>
        <path class="st15" d="M1545.2,659c-7.2,29.1-6.5,26.4-6.7,27.4c-0.6,8.9,23.8,10.4,24.1-0.2l-12.6-46.6c-1.2,4.7-2.2,9-3.1,12.7"/>
        <path class="st7" d="M1565.3,708.5c-5.5,3.2-5.5,8.4,0,11.6c5.5,3.2,14.5,3.2,20.1,0c5.5-3.2,5.5-8.4,0-11.6
		C1579.8,705.3,1570.8,705.4,1565.3,708.5z"/>
        <path class="st8" d="M1575.4,713.8h-0.5c-1.8,0-3.2-1.4-3.2-3.2v-15.1h6.8v15.1C1578.5,712.3,1577.2,713.8,1575.4,713.8z"/>
        <path class="st13" d="M1587,694.8l-12.6-46.6c-12.2,49.7-11.3,45.8-11.5,46.8C1562.3,703.9,1586.6,705.4,1587,694.8z"/>
        <path class="st10" d="M1587,694.8c-0.4,10.7-24.7,9.1-24.1,0.2v-0.1c7.3,2.9,19.6,1.4,18.9-6.2c-1.2-10.6-4.8-25.9-7.3-39.1
		c-1.9,7.6-3.6,13.8-5,18.9c1.3-5.5,3-12.1,4.9-20.2L1587,694.8L1587,694.8z"/>
        <path class="st15" d="M1569.1,669.5l-0.7,3c-5.9,23.9-5.3,21.6-5.5,22.5c-0.6,8.9,23.8,10.4,24.1-0.2l-12.6-46.6
		c-1.8,7.3-3.4,13.6-4.6,18.7"/>
        <path class="st7" d="M1536,720.7c-3.6,2-3.6,5.4,0,7.4s9.4,2,12.8,0c3.5-2,3.5-5.4,0-7.4S1539.5,718.7,1536,720.7z"/>
        <path class="st8" d="M1542.5,724.1h-0.4c-1.1,0-2-1-2-2v-9.7h4.4v9.7C1544.5,723.1,1543.5,724.1,1542.5,724.1z"/>
        <path class="st13" d="M1549.9,711.9l-8.2-29.9c-7.8,31.9-7.2,29.4-7.4,30.1C1534,717.7,1549.8,718.7,1549.9,711.9L1549.9,711.9z"/>
        <path class="st10" d="M1549.9,711.9c-0.2,6.8-15.9,5.9-15.5,0.1v-0.1c4.7,1.8,12.6,1,12.1-4c-0.8-6.8-3.1-16.7-4.7-25.1
		c-1.2,4.9-2.3,8.9-3.2,12.1c0.8-3.5,1.9-7.8,3.2-13L1549.9,711.9L1549.9,711.9z"/>
        <path class="st15" d="M1549.9,711.9l-8.2-29.9c-7.8,31.9-7.2,29.4-7.4,30.1C1534,717.7,1549.8,718.7,1549.9,711.9L1549.9,711.9z"/>
    </g>
    <g id="tree">

        <ellipse transform="matrix(5.419920e-03 -1 1 5.419920e-03 -377.6805 1378.7739)" class="st7" cx="504.3" cy="879.3" rx="3.6" ry="6.3"/>
        <path class="st8" d="M505,879.8h-1.4c-0.4,0-0.8-0.3-0.8-0.8v-15.6c0-0.4,0.3-0.8,0.8-0.8h1.4c0.4,0,0.8,0.3,0.8,0.8V879
		C505.7,879.4,505.4,879.8,505,879.8z"/>
        <path class="st9" d="M512.6,858.7c0,4.9-4,9-9,9c-2.6,0-4.9-1.1-6.5-2.8c-1.6-1.6-2.5-3.9-2.5-6.2c0-4.9,4-9,9-9
		c3.9,0,7.2,2.5,8.5,5.9C512.4,856.7,512.6,857.7,512.6,858.7z"/>
        <path class="st10" d="M512.1,855.7c-0.8,5.6-5.5,9.9-11.4,9.9c-1.3,0-2.5-0.3-3.7-0.6c-1.6-1.6-2.5-3.9-2.5-6.2c0-4.9,4-9,9-9
		C507.5,849.7,510.8,852.2,512.1,855.7L512.1,855.7z"/>
        <path class="st11" d="M512.6,858.7c0,4.9-4,9-9,9c-2.6,0-4.9-1.1-6.5-2.8c-1.6-1.6-2.5-3.9-2.5-6.2c0-4.9,4-9,9-9
		c3.9,0,7.2,2.5,8.5,5.9C512.4,856.7,512.6,857.7,512.6,858.7z"/>
        <ellipse transform="matrix(0.3628 -0.9319 0.9319 0.3628 -481.1823 1009.6872)" class="st3" cx="497.7" cy="856.7" rx="2.4" ry="1.5"/>
    </g>
</svg>

<div class="content">
    <svg id="upress-robocat" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
         viewBox="0 0 131.7 122.6">
        <style type="text/css">
            .robocat0{fill-rule:evenodd;clip-rule:evenodd;}
            .robocat1{fill-rule:evenodd;clip-rule:evenodd;fill:#1F88CA;}
            .robocat2{fill-rule:evenodd;clip-rule:evenodd;fill:#185F9E;}
            .robocat3{fill-rule:evenodd;clip-rule:evenodd;fill:#26ACEF;}
            .robocat4{fill-rule:evenodd;clip-rule:evenodd;fill:#151600;}
            .robocat5{fill-rule:evenodd;clip-rule:evenodd;fill:#FFFFFF;}
            .robocat6{fill-rule:evenodd;clip-rule:evenodd;fill:#E8E8E8;}
            .robocat7{fill:#0C87C9;}
            .robocat8{fill:#FFFFFF;}
            .robocat9{fill-rule:evenodd;clip-rule:evenodd;fill:#B1B1B1;}
            .robocat10{fill-rule:evenodd;clip-rule:evenodd;fill:#7E7E7E;}
            .robocat11{fill:none;robocatroke:#151600;robocatroke-miterlimit:10;}
        </style>
        <path class="robocat4" d="M125.9,3c-4.6-3.7-10.7-3.5-16.1-2c-3.1,0.8-6.2,2.1-9.1,3.6c-3.5,1.8-6.9,3.9-10.1,6.2
				c-6.5-2.5-13.4-4-20.4-4.4l0,0h0l-8.7,0h0l0,0c-7,0.4-13.9,1.9-20.4,4.5C37.9,8.4,34.5,6.3,31,4.5c-2.9-1.5-6-2.7-9.1-3.6
				c-3.9-1.1-8.3-1.5-12.3-0.1C8.2,1.4,6.9,2.1,5.8,3C1.4,6.6,0,12.3,0,18.1C0,21.5,0.5,25,1.1,28C1.9,32,3,36,4.5,39.9
				c0.8,2.2,1.8,4.4,3,6.5C5.9,51.6,5.1,57,4.9,62.4c0,0.8,0,1.6,0,2.3v10.9v15.2c0,3,0.6,6,1.7,8.9c2.7,6.8,8,12,14.6,15.1
				c0,0,10.7,4.7,14.6,5.4c14.8,2.8,45.7,3.3,60.4,0c3.8-0.9,14.6-5.5,14.6-5.5c6.5-3.1,11.7-8.2,14.4-15c1.1-2.8,1.7-5.8,1.7-8.9V64.8
				c0-6.2-0.8-12.4-2.6-18.4c1.2-2,2.1-4.3,3-6.5c1.5-3.9,2.6-7.9,3.4-11.9c0.5-2.5,0.9-5.3,1-8.2C131.9,13.5,130.8,6.9,125.9,3z"/>
        <path class="robocat1" d="M104.7,23.7c-10.4-8.8-24.5-13.5-38.9-13.6c-14.5,0.1-28.5,4.7-38.9,13.6C14.2,34.5,9.2,49.3,9.2,64.4v26
				c0,9.2,6.3,17.5,15.2,21.4c0.1,0,0.1,0.1,0.2,0.1c0,0,0.2,0.1,0.5,0.2c0.2,0.1,0.4,0.2,0.6,0.2c2.7,1,9.5,3.6,12.4,4.1
				c13.7,2.7,42.3,3.1,56,0c3.6-0.8,13.5-4.6,13.5-4.6c1.4-0.7,2.8-1.5,4.1-2.4c6.6-4.4,10.9-11.4,10.9-19v-26
				C122.5,49.3,117.5,34.5,104.7,23.7z"/>
        <path class="robocat1" d="M8.3,5.9c-8.6,6.9-2.2,28.9,2.2,37.4c3-8.1,7.9-15.3,14.9-21.3c1.9-1.6,3.9-3.1,6.1-4.4c-5.3-4-15.3-8.1-19.1-5
				c-3.9,3.1-4.3,10.9-3.1,16.3C7.5,22,7.1,14.3,11.1,11c5.2-4.2,15.7,1,22,5.6c1.8-1.1,3.6-2,5.5-2.9C30,7.4,15.5,0.1,8.3,5.9z"/>
        <path class="robocat2" d="M123.4,5.9c8.6,6.9,2.2,28.9-2.2,37.4c-3-8.1-7.9-15.3-14.9-21.3c-1.9-1.6-4-3.1-6.1-4.5c5.3-4,15.2-8.1,19.1-5
				c3.9,3.1,4.3,10.9,3.1,16.3c1.7-6.8,2.2-14.6-1.8-17.8c-5.2-4.2-15.6,1-21.9,5.6c-1.8-1-3.6-2-5.5-2.8
				C101.6,7.4,116.1,0.1,123.4,5.9z"/>
        <path class="robocat3" d="M41.1,16.9c6.9-2.8,13.1-3.6,13.9-1.7c0.8,1.9-4.2,5.7-11.1,8.5C37,26.6,30.8,27.3,30,25.4
				C29.2,23.6,34.2,19.7,41.1,16.9z"/>
        <path class="robocat2" d="M104.8,23.7c-10.4-8.8-24.5-13.5-38.9-13.6c-2.6,0-5.1,0.2-7.7,0.5c0.1,0,0.2,0,0.3,0
				c14.5,0.1,28.5,4.7,38.9,13.6c12.7,10.8,17.7,25.6,17.7,40.8v26c0,7.6-4.3,14.6-10.9,19c-1.3,0.9-2.6,1.7-4.1,2.4
				c0,0-10,3.8-13.5,4.6c-2.9,0.7-6.5,1.2-10.6,1.5c6.9-0.3,13.3-0.9,17.9-2c3.6-0.8,13.5-4.6,13.5-4.6c1.4-0.7,2.8-1.5,4.1-2.4
				c6.6-4.4,10.9-11.4,10.9-19v-26C122.6,49.3,117.5,34.5,104.8,23.7z"/>
        <path class="robocat4" d="M69.9,102.6h-8l-2.8,4.9H37.7c-2.8,0-5.4-0.7-7.9-1.9c-6.8-3.5-10.9-10.8-10.9-18.4V71.4c0-1.8,0.3-3.5,0.9-5.2
				c2.6-7.3,10.4-11.6,17.3-14.1c8.9-3.2,19.3-4.4,28.8-4.4s19.8,1.2,28.8,4.4c6.9,2.5,14.7,6.8,17.3,14.1c0.6,1.7,0.9,3.4,0.9,5.2
				v15.8c0,7.6-4,14.9-10.9,18.4c-2.5,1.2-5.1,1.9-7.9,1.9H72.8l-0.8-1.4L69.9,102.6z"/>
        <path class="robocat5" d="M20.7,71.4v5.1v4.9v0.8v3.2v1.8c0,10.2,7.6,18.5,17,18.5H58l0.3-0.6l2.5-4.4h4.2v-7.2c0-2.5,0.3-2.7-2-3.8
				c-2.5-1.3-4.8-2-4.8-2.9c0-3.1,15.1-3.1,15.1,0c0,0.9-2.2,1.6-4.7,2.9c-2.4,1.2-2.1,1.3-2.1,3.9v7.2h4.2l2.5,4.4l0.3,0.6h20.3
				c9.3,0,17-8.3,17-18.5v-4v-1v-0.8v-1.5v-8.5C111.1,42.1,20.7,42.1,20.7,71.4z M40.4,94.3c-6.8,0-12.4-5.3-12.8-12.1h0l0.1-0.8h3
				l0,0.1h0c0,0.1,0,0.1,0,0.2l0,0.1l0,0c0.1,5.3,4.4,9.5,9.7,9.5c5.4,0,9.7-4.3,9.7-9.7h0l0-0.1h3l0,0.6
				C52.9,88.9,47.3,94.3,40.4,94.3z M104.1,83.2c-0.8,6.3-6.2,11.1-12.7,11.1c-7.1,0-12.8-5.7-12.8-12.8h-0.1v-0.1h3.1l0.1,0.3
				c0.1,5.3,4.4,9.5,9.7,9.5c5,0,9.1-3.8,9.6-8.7l0,0l0,0c0-0.1,0-0.2,0-0.3l0.1-0.8h3L104.1,83.2z"/>
    </svg>

    <h1><?php echo $strings['ssshhh']; ?></h1>
    <h2>
        <?php echo $strings['website_in_maintenance_mode']; ?>
    </h2>
</div>

</body>
</html>
