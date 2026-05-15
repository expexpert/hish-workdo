<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Paiement réussi</title>

    <style>
        body {
            margin: 0px;
            padding: 0px;
        }

        .Payment-successful * {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        section.Payment-successful {
            background: #E7E8E9;
            position: fixed;
            width: 100%;
            height: 100%;
        }

        .payment-boxx {
            position: relative;
            height: 100vh;
        }

        .payment-text-box {
            background: #fff;
            padding: 50px;
            width: fit-content;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 0px solid #E5E7EB;
            box-shadow: 0px 0px 10px 0px #00000040;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Centers the button and text */
        }

        .payment-text-box * {
            text-align: center;
        }

        .payment-text-box h2 {
            font-size: 42px;
            margin: 0px;
            font-weight: 600;
            color: #363636;
        }

        .payment-text-box p {
            font-size: 20px;
            margin: 10px 0px 0px 0px;
            max-width: 370px;
            color: #666;
        }

        .payment-text-box h6 {
            margin: 20px 0px 0px 0px;
            font-size: 25px;
            color: #363636;
            font-weight: 600;
        }

        /* --- New Button Styles --- */
        #continueBtn {
            margin-top: 30px;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: 600;
            color: #ffffff;
            background-color: #007AFF;
            /* Modern blue */
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.1s ease;
            width: 100%;
            /* Makes button fill the width of the text block */
            max-width: 350px;
        }

        #continueBtn:hover {
            background-color: #0056b3;
        }

        #continueBtn:active {
            transform: scale(0.98);
            /* Slight click effect */
        }
    </style>
</head>

<body>

    <section class="Payment-successful">
        <div class="payment-boxx">
            <div class="payment-text-box">
                <h2>Paiement réussi.</h2>
                <p>Vous pouvez maintenant vous connecter à l'application.</p>
                <h6>Merci.</h6>

                <button id="continueBtn" onclick="window.location.href='myapp://subscription-success'">Continuer sur l'application</button>
            </div>
        </div>
    </section>
</body>

</html>

<!-- 
<script>
    setTimeout(() => {
        window.location.href = '/';
    }, 8000);
</script> -->