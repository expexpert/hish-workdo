<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SimplyCompta - Coming Soon</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">

  <style>
    :root{
      --blue-1:#2f7ef7;
      --blue-2:#66b6ff;
      --blue-3:#8fd1ff;
      --blue-4:#d7f1ff;
      --blue-5:#1b57c9;
      --green-1:#b8f11e;
      --green-2:#87d80f;
      --green-3:#5fb400;
      --white:#ffffff;
      --text:#11408b;
      --shadow:0 14px 30px rgba(13,67,153,.22);
    }

    *{
      box-sizing:border-box;
    }

    body{
      margin:0;
      min-height:100vh;
      font-family:Arial, Helvetica, sans-serif;
      color:#fff;
      background:
        radial-gradient(circle at 50% 38%, #bfe9ff 0%, #90d3ff 24%, #5daefc 52%, #3d86ed 74%, #2f6fe0 100%);
      overflow-x:hidden;
    }

    .page{
      width:100%;
      min-height:100vh;
      position:relative;
      overflow:hidden;
      padding:34px 20px 44px;
    }

    /* clouds */
    .cloud{
      position:absolute;
      background:rgba(255,255,255,.28);
      border-radius:60px;
      filter:blur(.2px);
      z-index:0;
    }

    .cloud:before,
    .cloud:after{
      content:"";
      position:absolute;
      background:inherit;
      border-radius:50%;
    }

    .cloud.c1{width:130px;height:40px;top:130px;left:-20px;}
    .cloud.c1:before{width:48px;height:48px;left:10px;top:-18px;}
    .cloud.c1:after{width:58px;height:58px;left:48px;top:-28px;}

    .cloud.c2{width:148px;height:44px;top:128px;right:-18px;}
    .cloud.c2:before{width:48px;height:48px;right:18px;top:-15px;}
    .cloud.c2:after{width:62px;height:62px;right:56px;top:-27px;}

    .cloud.c3{width:96px;height:32px;top:307px;right:26px;opacity:.24;}
    .cloud.c3:before{width:34px;height:34px;left:12px;top:-12px;}
    .cloud.c3:after{width:44px;height:44px;left:40px;top:-18px;}

    .cloud.c4{width:110px;height:36px;bottom:80px;left:-10px;opacity:.22;}
    .cloud.c4:before{width:38px;height:38px;left:10px;top:-14px;}
    .cloud.c4:after{width:52px;height:52px;left:42px;top:-22px;}

    .cloud.c5{width:122px;height:36px;bottom:40px;right:12px;opacity:.24;}
    .cloud.c5:before{width:42px;height:42px;left:14px;top:-14px;}
    .cloud.c5:after{width:58px;height:58px;left:44px;top:-24px;}

    .container{
      position:relative;
      z-index:2;
      max-width:920px;
      margin:0 auto;
      text-align:center;
    }

    /* logo */
    .logo{
      display:flex;
      align-items:center;
      justify-content:center;
      gap:18px;
      margin-bottom:18px;
    }

    .logo-mark{
      position:relative;
      width:86px;
      height:86px;
      border-radius:50%;
      background:linear-gradient(180deg, #ffffff 0%, #ebf6ff 100%);
      box-shadow:0 10px 18px rgba(0,0,0,.14);
      flex-shrink:0;
    }

    .logo-mark:before{
      content:"";
      position:absolute;
      width:44px;
      height:20px;
      border-left:11px solid transparent;
      border-bottom:11px solid transparent;
      transform:rotate(-45deg);
      left:15px;
      top:32px;
      box-shadow:none;
      background:transparent;
      border-right:0;
    }

    .logo-mark:after{
      content:"";
      position:absolute;
      left:18px;
      top:20px;
      width:46px;
      height:28px;
      border-left:12px solid #4b8ef5;
      border-bottom:12px solid #4b8ef5;
      transform:rotate(-45deg);
      border-radius:3px;
    }

    .logo-text{
      font-family:"Baloo 2", system-ui, sans-serif;
      font-size:66px;
      line-height:1;
      font-weight:800;
      letter-spacing:-1px;
      color:#ffffff;
      text-shadow:0 4px 10px rgba(0,0,0,.14);
    }

    .logo-text span{
      color:#104fbf;
    }

    /* title */
    .coming-wrap{
      margin-top:10px;
      line-height:.9;
    }

    .big-title{
      margin:0;
      font-family:"Baloo 2", system-ui, sans-serif;
      font-size:160px;
      font-weight:800;
      letter-spacing:-4px;
      color:#f5f8ff;
      text-shadow:
        0 2px 0 #dfe8f7,
        0 4px 0 #c8d5eb,
        0 7px 0 #b4c5df,
        0 18px 26px rgba(30,75,155,.35);
    }

    .big-title.green{
      color:var(--green-1);
      text-shadow:
        0 2px 0 #90d80c,
        0 5px 0 #78c30a,
        0 9px 0 #5faa05,
        0 16px 22px rgba(69,126,0,.35);
      margin-top:-10px;
    }

    .sub-script{
      margin-top:18px;
      font-family:"Pacifico", cursive;
      font-size:54px;
      color:#f7fbff;
      text-shadow:0 4px 10px rgba(35,73,148,.25);
      position:relative;
      display:inline-block;
    }

    .sub-script:after{
      content:"";
      position:absolute;
      left:12%;
      right:12%;
      bottom:-14px;
      height:6px;
      border-radius:99px;
      background:rgba(255,255,255,.92);
      box-shadow:0 2px 8px rgba(27,87,201,.2);
    }

    /* middle section */
    .middle{
      margin-top:58px;
      display:flex;
      align-items:center;
      justify-content:center;
      gap:60px;
      flex-wrap:wrap;
    }

    .illus-left,
    .illus-right{
      position:relative;
    }

    /* laptop block */
    .leaf{
      position:absolute;
      width:78px;
      height:40px;
      background:linear-gradient(135deg,#8add1d,#5abb0e);
      border-radius:0 40px 0 40px;
      z-index:0;
      transform:rotate(-18deg);
      left:-14px;
      bottom:14px;
      box-shadow:0 10px 20px rgba(67,153,0,.15);
    }

    .leaf:after{
      content:"";
      position:absolute;
      width:2px;
      height:28px;
      background:#4a9409;
      left:34px;
      top:6px;
      transform:rotate(42deg);
    }

    .laptop{
      width:300px;
      position:relative;
      z-index:1;
    }

    .screen{
      height:168px;
      border-radius:10px 10px 0 0;
      background:linear-gradient(180deg,#2e73de 0%, #1d53b7 100%);
      border:4px solid #cfe3ff;
      box-shadow:var(--shadow);
      padding:12px;
      position:relative;
      overflow:hidden;
    }

    .screen-ui{
      width:100%;
      height:100%;
      background:#f4f9ff;
      border-radius:6px;
      display:flex;
      overflow:hidden;
    }

    .sidebar{
      width:58px;
      background:#285bc5;
      padding:8px 6px;
      display:flex;
      flex-direction:column;
      gap:7px;
    }

    .side-line{
      height:8px;
      border-radius:99px;
      background:rgba(255,255,255,.65);
    }

    .dashboard{
      flex:1;
      padding:12px 10px;
      display:flex;
      flex-direction:column;
      gap:10px;
    }

    .chart{
      height:48px;
      border-radius:8px;
      background:linear-gradient(180deg,#eef6ff,#dcecff);
      position:relative;
      overflow:hidden;
    }

    .chart:after{
      content:"";
      position:absolute;
      left:10px;
      right:10px;
      top:14px;
      height:22px;
      background:
        linear-gradient(90deg,
          #68aef7 0 10%,
          transparent 10% 12%,
          #8dd64a 12% 26%,
          transparent 26% 28%,
          #68aef7 28% 42%,
          transparent 42% 44%,
          #2f7ef7 44% 56%,
          transparent 56% 58%,
          #8dd64a 58% 74%,
          transparent 74% 76%,
          #68aef7 76% 88%);
      border-radius:4px;
      opacity:.9;
    }

    .list-line{
      height:10px;
      border-radius:99px;
      background:#d6e7ff;
    }

    .base{
      height:20px;
      background:linear-gradient(180deg,#e6efff 0%, #c9d9f5 100%);
      border-radius:0 0 18px 18px;
      position:relative;
      box-shadow:0 12px 22px rgba(39,92,189,.18);
    }

    .base:after{
      content:"";
      position:absolute;
      left:50%;
      top:4px;
      width:74px;
      height:8px;
      transform:translateX(-50%);
      border-radius:999px;
      background:#b7caec;
    }

    .calendar{
      position:absolute;
      right:-12px;
      bottom:-14px;
      width:116px;
      height:104px;
      background:#fdfefe;
      border-radius:12px;
      box-shadow:0 12px 22px rgba(38,88,173,.22);
      overflow:hidden;
      border:3px solid #d7e6ff;
    }

    .calendar .head{
      height:22px;
      background:linear-gradient(90deg,#84d919,#c9f14a);
    }

    .calendar .grid{
      display:grid;
      grid-template-columns:repeat(5,1fr);
      gap:7px;
      padding:12px 10px 8px;
    }

    .calendar .cell{
      height:8px;
      background:#b5d97c;
      border-radius:99px;
      opacity:.8;
    }

    .rings{
      position:absolute;
      top:-10px;
      left:18px;
      display:flex;
      gap:18px;
    }

    .rings span{
      width:10px;
      height:20px;
      border:4px solid #b4c7e3;
      border-bottom:none;
      border-radius:10px 10px 0 0;
      background:transparent;
    }

    .clock{
      position:absolute;
      left:126px;
      bottom:-20px;
      width:78px;
      height:78px;
      background:radial-gradient(circle at 35% 30%, #ffffff 0%, #f3f8ff 75%);
      border-radius:50%;
      border:8px solid #3f8af1;
      box-shadow:0 10px 16px rgba(39,92,189,.2);
    }

    .clock:before,
    .clock:after{
      content:"";
      position:absolute;
      left:50%;
      top:50%;
      transform-origin:bottom center;
      background:#79bc12;
      border-radius:99px;
    }

    .clock:before{
      width:4px;
      height:18px;
      transform:translate(-50%,-100%) rotate(42deg);
    }

    .clock:after{
      width:4px;
      height:24px;
      transform:translate(-50%,-100%) rotate(-44deg);
      background:#2f7ef7;
    }

    .clock .pin{
      position:absolute;
      width:10px;
      height:10px;
      border-radius:50%;
      background:#6ab311;
      left:50%;
      top:50%;
      transform:translate(-50%,-50%);
    }

    .calc{
      position:absolute;
      right:8px;
      bottom:-12px;
      width:72px;
      height:86px;
      border-radius:10px;
      background:linear-gradient(180deg,#3e6288,#1d3552);
      box-shadow:0 10px 16px rgba(26,56,104,.25);
      padding:10px 8px;
    }

    .calc .screen-small{
      height:16px;
      border-radius:4px;
      background:#a7f06f;
      margin-bottom:8px;
    }

    .calc .keys{
      display:grid;
      grid-template-columns:repeat(3,1fr);
      gap:5px;
    }

    .calc .key{
      height:11px;
      border-radius:3px;
      background:#d6e3f7;
    }

    /* megaphone */
    .megaphone{
      width:230px;
      height:180px;
      position:relative;
      transform:rotate(22deg);
    }

    .cone{
      position:absolute;
      right:34px;
      top:18px;
      width:130px;
      height:100px;
      background:linear-gradient(90deg,#ffffff 0%, #eaf5ff 60%, #cfe3ff 100%);
      clip-path:polygon(0 20%, 100% 0, 100% 100%, 0 80%);
      border-radius:10px;
      box-shadow:0 14px 24px rgba(39,92,189,.2);
      border:4px solid #d7e8ff;
    }

    .cone-inner{
      position:absolute;
      right:98px;
      top:40px;
      width:58px;
      height:58px;
      border-radius:50%;
      background:radial-gradient(circle at 35% 35%, #2f7ef7 0%, #144cad 58%, #0d3d97 100%);
      border:6px solid #d9ebff;
      z-index:2;
    }

    .green-band{
      position:absolute;
      right:8px;
      top:48px;
      width:44px;
      height:54px;
      background:linear-gradient(180deg,#baf01f,#6fc108);
      border-radius:6px;
      z-index:3;
      box-shadow:0 10px 16px rgba(83,152,0,.18);
    }

    .blue-band{
      position:absolute;
      right:-16px;
      top:52px;
      width:36px;
      height:48px;
      background:linear-gradient(180deg,#458ef4,#225fda);
      border-radius:0 10px 10px 0;
      z-index:2;
    }

    .handle{
      position:absolute;
      right:22px;
      top:102px;
      width:48px;
      height:76px;
      background:linear-gradient(180deg,#3b86ef,#184eb9);
      border-radius:10px;
      transform:rotate(18deg);
      box-shadow:0 10px 18px rgba(39,92,189,.2);
    }

    .handle:before{
      content:"";
      position:absolute;
      inset:8px 10px 14px;
      border-radius:8px;
      background:linear-gradient(180deg,#6aaaf8,#2f73e1);
    }

    .burst{
      position:absolute;
      width:14px;
      height:44px;
      background:#ffffff;
      border-radius:999px;
      opacity:.95;
      box-shadow:0 4px 10px rgba(39,92,189,.12);
    }

    .burst.b1{right:178px;top:18px;transform:rotate(-64deg);}
    .burst.b2{right:163px;top:42px;transform:rotate(-94deg);height:34px;}
    .burst.b3{right:146px;top:14px;transform:rotate(-35deg);height:24px;background:#78d110;}

    /* loading */
    .loading-wrap{
      margin-top:44px;
    }

    .loading-label{
      font-family:"Baloo 2", system-ui, sans-serif;
      font-weight:700;
      font-size:42px;
      text-shadow:0 4px 10px rgba(35,73,148,.22);
      margin-bottom:16px;
    }

    .progress-shell{
      width:min(760px, 88%);
      height:40px;
      margin:0 auto;
      background:linear-gradient(180deg,#7fc3ff 0%, #4d95ef 100%);
      border-radius:999px;
      box-shadow:inset 0 0 0 5px rgba(220,239,255,.65), 0 8px 18px rgba(30,75,155,.16);
      padding:6px;
    }

    .progress-track{
      height:100%;
      border-radius:999px;
      background:linear-gradient(180deg,#a3d9ff 0%, #5a9cf2 100%);
      position:relative;
      overflow:hidden;
    }

    .progress-fill{
      width:62%;
      height:100%;
      border-radius:999px;
      background:linear-gradient(90deg,var(--green-2) 0%, #a7ed2c 82%, #2f7ef7 82%, #2f7ef7 100%);
      box-shadow:inset 0 -3px 0 rgba(0,0,0,.08);
      position:relative;
      animation:moveBar 2.6s ease-in-out infinite alternate;
    }

    @keyframes moveBar{
      from{width:58%}
      to{width:68%}
    }

    /* lower content */
    .divider{
      width:min(840px, 92%);
      height:3px;
      margin:42px auto 24px;
      background:rgba(255,255,255,.35);
      border-radius:999px;
    }

    .info{
      max-width:720px;
      margin:0 auto;
      text-align:left;
      padding:0 10px;
    }

    .script-title{
      font-family:"Pacifico", cursive;
      font-size:42px;
      color:#fff;
      text-shadow:0 4px 10px rgba(35,73,148,.22);
      margin-bottom:16px;
    }

    .row{
      display:flex;
      align-items:center;
      gap:16px;
      margin:16px 0;
      font-family:"Baloo 2", system-ui, sans-serif;
      font-size:32px;
      font-weight:700;
      text-shadow:0 4px 10px rgba(35,73,148,.18);
      flex-wrap:wrap;
    }

    .icon-mail,
    .icon-fb,
    .icon-in{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      flex-shrink:0;
      box-shadow:0 8px 15px rgba(39,92,189,.18);
    }

    .icon-mail{
      width:58px;
      height:42px;
      border:3px solid #f4fbff;
      border-radius:6px;
      position:relative;
    }

    .icon-mail:before,
    .icon-mail:after{
      content:"";
      position:absolute;
      width:30px;
      height:3px;
      background:#f4fbff;
      top:16px;
    }

    .icon-mail:before{
      left:3px;
      transform:rotate(35deg);
    }

    .icon-mail:after{
      right:3px;
      transform:rotate(-35deg);
    }

    .socials{
      display:inline-flex;
      gap:10px;
      align-items:center;
    }

    .icon-fb,
    .icon-in{
      width:46px;
      height:46px;
      border-radius:8px;
      background:linear-gradient(180deg,#ffffff 0%, #eaf5ff 100%);
      color:#2462d9;
      font-family:"Baloo 2", system-ui, sans-serif;
      font-size:32px;
      font-weight:800;
    }

    .icon-in{
      font-size:24px;
      font-weight:700;
    }

    .bottom-script{
      margin-top:26px;
      font-family:"Pacifico", cursive;
      font-size:42px;
      color:#fff;
      text-shadow:0 4px 10px rgba(35,73,148,.22);
      text-align:center;
      position:relative;
      display:inline-block;
    }

    .bottom-script:after{
      content:"";
      position:absolute;
      left:26%;
      right:26%;
      bottom:-14px;
      height:6px;
      border-radius:999px;
      background:rgba(255,255,255,.92);
    }

    .footer-center{
      text-align:center;
    }

    @media (max-width: 900px){
      .logo-text{font-size:50px;}
      .big-title{font-size:120px;}
      .sub-script{font-size:42px;}
    }

    @media (max-width: 640px){
      .page{padding-top:22px;}
      .logo{gap:12px;}
      .logo-mark{width:62px;height:62px;}
      .logo-text{font-size:38px;}
      .big-title{font-size:78px;letter-spacing:-2px;}
      .big-title.green{margin-top:0;}
      .sub-script{font-size:28px;}
      .middle{gap:26px;margin-top:36px;}
      .laptop{width:250px;}
      .megaphone{width:180px;height:140px;}
      .loading-label{font-size:28px;}
      .progress-shell{height:30px;}
      .script-title{font-size:30px;}
      .row{font-size:22px;}
      .bottom-script{font-size:28px;}
      .info{text-align:center;}
      .row{justify-content:center;}
    }
  </style>
</head>
<body>
  <div class="page">
    <div class="cloud c1"></div>
    <div class="cloud c2"></div>
    <div class="cloud c3"></div>
    <div class="cloud c4"></div>
    <div class="cloud c5"></div>

    <div class="container">
      <div class="logo">
        <div class="logo-mark"></div>
        <div class="logo-text">Simply<span>Compta</span></div>
      </div>

      <div class="coming-wrap">
        <h1 class="big-title">COMING</h1>
        <h1 class="big-title green">SOON!</h1>
      </div>

      <div class="sub-script">Notre nouveau site arrive bientôt !</div>

      <div class="middle">
        <div class="illus-left">
          <div class="leaf"></div>

          <div class="laptop">
            <div class="screen">
              <div class="screen-ui">
                <div class="sidebar">
                  <div class="side-line"></div>
                  <div class="side-line"></div>
                  <div class="side-line"></div>
                  <div class="side-line"></div>
                  <div class="side-line"></div>
                  <div class="side-line"></div>
                </div>
                <div class="dashboard">
                  <div class="chart"></div>
                  <div class="list-line"></div>
                  <div class="list-line"></div>
                  <div class="list-line"></div>
                  <div class="chart"></div>
                </div>
              </div>
            </div>

            <div class="base"></div>

            <div class="calendar">
              <div class="rings"><span></span><span></span></div>
              <div class="head"></div>
              <div class="grid">
                <div class="cell"></div><div class="cell"></div><div class="cell"></div><div class="cell"></div><div class="cell"></div>
                <div class="cell"></div><div class="cell"></div><div class="cell"></div><div class="cell"></div><div class="cell"></div>
                <div class="cell"></div><div class="cell"></div><div class="cell"></div><div class="cell"></div><div class="cell"></div>
                <div class="cell"></div><div class="cell"></div><div class="cell"></div><div class="cell"></div><div class="cell"></div>
              </div>
            </div>

            <div class="clock"><div class="pin"></div></div>

            <div class="calc">
              <div class="screen-small"></div>
              <div class="keys">
                <div class="key"></div><div class="key"></div><div class="key"></div>
                <div class="key"></div><div class="key"></div><div class="key"></div>
                <div class="key"></div><div class="key"></div><div class="key"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="illus-right">
          <div class="megaphone">
            <div class="burst b1"></div>
            <div class="burst b2"></div>
            <div class="burst b3"></div>
            <div class="cone"></div>
            <div class="cone-inner"></div>
            <div class="green-band"></div>
            <div class="blue-band"></div>
            <div class="handle"></div>
          </div>
        </div>
      </div>

      <div class="loading-wrap">
        <div class="loading-label">Chargement en cours...</div>
        <div class="progress-shell">
          <div class="progress-track">
            <div class="progress-fill"></div>
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <div class="info">
        <div class="script-title">Restez informé !</div>

        <div class="row">
          <span class="icon-mail"></span>
          <span>Inscrivez-vous à notre newsletter</span>
        </div>

        <div class="row">
          <span>Suivez-nous :</span>
          <span class="socials">
            <span class="icon-fb">f</span>
            <span class="icon-in">in</span>
          </span>
        </div>
      </div>

      <div class="divider"></div>

      <div class="footer-center">
        <div class="bottom-script">Patience, de belles surprises vous attendent !</div>
      </div>
    </div>
  </div>
</body>
</html>