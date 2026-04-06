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
    .icon-in,
    .icon-ig,
    .icon-tt{
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
    .icon-in,
    .icon-ig,
    .icon-tt{
      width:46px;
      height:46px;
      border-radius:8px;
      background:linear-gradient(180deg,#ffffff 0%, #eaf5ff 100%);
      color:#2462d9;
    }

    .socials span svg{
      width:24px;
      height:24px;
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
      <div class="logo" style="margin-bottom: 42px;">
        <!-- <div class="logo-mark"></div>
        <div class="logo-text">Simply<span>Compta</span></div> -->
        <img src="{{ asset('storage/uploads/new-landing-page/header_logo.svg')}}" alt="SimplyCompta">
      </div>

      <div class="coming-wrap">
        <h1 class="big-title">COMING</h1>
        <h1 class="big-title green">SOON!</h1>
      </div>

      <div class="sub-script">SimplyCompta arrive. Respirez. Votre cabinet va rentré dans une nouvelle dimension</div>

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

        <!-- <div class="row">
          <span class="icon-mail"></span>
          <span>Inscrivez-vous à notre newsletter</span>
        </div> -->

        <div class="row">
          <span>Suivez-nous :</span>
          <span class="socials">
            <a href ="https://www.facebook.com/share/1PCo6LoQpo/?mibextid=wwXIfr" target="_blank"><span class="icon-fb"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/></svg></span></a>
            <a href="https://www.linkedin.com/in/simply-compta-6a92923ba?utm_source=share_via&utm_content=profile&utm_medium=member_ios" target="_blank"><span class="icon-in"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg></span></a>
            <a href="https://www.instagram.com/simplycompta?igsh=MW8zeHo4ZHFtdHBvbw==" target="_blank"><span class="icon-ig"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.266.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></span></a>
            <a href="https://www.tiktok.com/@simplycompta?_r=1&_t=ZS-94zG4bhoI5N" target="_blank"><span class="icon-tt"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.9-.32-1.98-.23-2.81.36-.54.38-.89.98-1.02 1.63-.14.76.07 1.53.57 2.13.58.68 1.48 1.07 2.38 1.03.92-.01 1.84-.46 2.41-1.18.61-.74.88-1.72.88-2.66.03-5.28.02-10.55.03-15.83z"/></svg></span></a>
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