<!DOCTYPE html>

<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Tabby Checkout</title>

    <style>
      * {
        margin: 0;
        padding: 0;
      }

      html,
      body {
        width: 100%;
        height: 100%;
        font-family: Verdana, Geneva, Tahoma, sans-serif;
      }

      #tabby-checkout {
        width: 100%;
        height: 100%;
      }

      .content {
        padding: 20px;
      }

      .button {
        height: 42px;
        background: #3eedbf;
        border-radius: 3px;
        text-transform: uppercase;
        font-size: 14px;
        transition: background 0.1s;
        cursor: pointer;
        padding: 0;
        border: 0;
        outline: 0;
        width: 100%;
        margin-bottom: 20px;
      }

      .control {
        background: white;
        border: 1px solid #505050;
        text-transform: initial;
      }

      .control {
        margin-top: 100px;
      }

      .control + .control {
        margin-top: 20px;
      }

      .button.primary:hover {
        background: rgba(62, 237, 191, 0.8);
      }

      .button.primary:active {
        background: #63debe;
      }

      .button.primary.button__disabled {
        background: #bdbdbd;
        color: #868686;
        cursor: default;
      }
    </style>
  </head>

  <body>
    <div class="content">
      <button id="payLaterButton" disabled class="button primary button__disabled">
        Pay in 14 days with tabby
      </button>

      <button id="installmentsButton" disabled class="button primary button__disabled">
        Pay in installments with tabby
      </button>
    </div>

    <!–– LINK integration.js ON CHECKOUT/PAYMENT OPTIONS PAGE  -->
    <script
      type="text/javascript"
      src="https://checkout.tabby.ai/integration.js"
    ></script>

    <!–– ALL CALLS ARE ASYNCHRONOUS, WE STRONGLY RECOMMEND TO INITIALIZE SESSION AS SOON AS POSSIBLE  -->
    <!–– THIS WAY EVERYTHING WOULD BE READY WHEN CUSTOMER DESIDES TO CHOOSE A PAYMENT METHOD  -->
    <script>
      (() => {

        // BUILD A PAYMENT OBJECT
        var payment = {
          amount: 1000,
          buyer: {
            dob: '1987-10-20',
            email: 'successful.payment@tabby.ai',
            name: 'John Doe',
            phone: '+971500000001' // +97150000001 for UAE, +96650000001  for Saudi Arabia, +96590000001 for Kuwait, +97366900001 for Bahrain
          },
          buyer_history: {
            loyalty_level: 10,
            registered_since: '2019-10-05T17:45:17+00:00',
            wishlist_count: 421,
            "is_social_networks_connected": true,
            "is_phone_number_verified": false,
            "is_email_verified": true
          },
          currency: 'AED',
          description: 'tabby Store Order #3',
          order: {
            items: [
              {
                description: 'To be displayed in tabby order information',
                product_url: 'https://tabby.store/p/SKU123',
                quantity: 1,
                reference_id: 'SKU123',
                title: 'Sample Item #1',
                unit_price: '300',
              },
              {
                description: 'To be displayed in tabby order information',
                product_url: 'https://tabby.store/p/SKU124',
                quantity: 1,
                reference_id: 'SKU124',
                title: 'Sample Item #2',
                unit_price: '9000',
              },
            ],
            reference_id: '#xxxx-xxxxxx-xxxx',
            shipping_amount: '50',
            tax_amount: '500',
          },
          order_history: [
            {
              amount: '1000',
              buyer: {
                name: 'John Doe',
                phone: '+971-505-5566-33',
              },
              items: [
                {
                  quantity: 4,
                  title: 'Sample Item #3',
                  unit_price: '250',
                  reference_id: 'item-sku',
                  ordered: 4,
                  captured: 4,
                  shipped: 4,
                  refunded: 1
                },
              ],
              payment_method: 'CoD',
              purchased_at: '2019-10-05T18:45:17+00:00',
              shipping_address: {
                address: 'Sample Address #1',
                city: 'Dubai'
              },
              status: 'complete',
            },
          ],
          shipping_address: {
            address: 'Sample Address #2',
            city: 'Dubai',
          }
        };

        // // THIS IS USED TO CONTROL RECREATION OF tabby POPUP
        var relaunchTabby = false;
        var selectedTabbyProduct = Tabby.PAYLATER;

        // // IN THIS DEMO WE HAVE TWO BUTTONS, DEPENDS ON CHECKOUT IMPLEMENTATION
        var payLaterButton = document.querySelector('#payLaterButton');
        var installmentsButton = document.querySelector('#installmentsButton');

        // // LETS SETUP A CONFIG FOR tabby SDK
        var config = {
           merchantCode: 'sa_store', // DEFAULT FOR SINGLE STORE WITH SINGLE COUNTRY. PLEASE CONTACT YOUR INTEGRATION MANAGER TO CONFIRM
           sessionId: '', 
           merchantUrls: {
           success: 'https://your-store.com/success',
           cancel: 'https://your-store.com/cancel',
           failure: 'https://your-store.com/failure',
          },
          nodeId: 'tabby-checkout',
          apiKey: 'dsgWmKHm3WBjksAidamcJNWkHEDHZpIy',
          lang: 'en', // ar, ar_AE, ar_BH, ar_DZ, ar_EG, ar_IN, ar_IQ, ar_JO, ar_KW, ar_LB, ar_LY, ar_MA, ar_OM, ar_QA, ar_SA, ar_SD, ar_SS, ar_SY, ar_TN, ar_YE are treated as Arabic language, the rest is mapped to English
          payment,
          onChange: data => {
            // tabby API RESPONSES ARE FULLY PROPAGATED THROUGH SDK

            if (data.status === 'created' || config.sessionId) {

                // ACTIVATE CONTROLS SO USER CAN CLICK IT

                if(data.products.payLater) {
                  payLaterButton.classList.remove('button__disabled');
                  payLaterButton.removeAttribute('disabled');
                }

                if(data.products.installments) {
                  installmentsButton.classList.remove('button__disabled');
                  installmentsButton.removeAttribute('disabled');
                }

                // IMPORTANT PIECE, TELLS SDK IF POPUP NEEDS TO BE RENDERED AGAIN
                // PLEASE KEEP IT THIS WAY
                if (relaunchTabby) {
                    Tabby.launch({product: selectedTabbyProduct});
                }
            }

           if(data.payment?.status === 'authorized') {
             setTimeout(() => {
               // OR USE `if(data.payment?.status === ‘closed’)` IN CASE CAPTURE ON AUTHORIZE IS SET TO TRUE
               // WE'VE AUTHORIZED/CLOSED GIVEN PAYMENT, IT'S OK TO PASS IT TO BACKEND NOW, PLEASE VERIFY IT WITH SERVER TO SERVER CALL AND SECRET KEY
               // ...YOUR CODE (REDIRECT TO SUCCESSFUL ORDER PAGE)
               alert("Please add your redirect to success page into the source code here if needed");
               Tabby.destroy();
             }, 3000);
           }

            console.log('SESSION:', data.status);
            console.log('AVAILABLE PRODUCTS:', data.products);
          },
          onClose: () => {
            // IMPORTANT PIECE, IF USER CLOSES tabby POPUP, WE NEED TO REDRAW IT, CHANCES ARE SHE CHOOSES ANOTHER PRODUCT
            relaunchTabby = true;
            document.querySelector('#tabby-checkout').style.display = 'none';
          },
        };

        // THIS SNIPPET DEMOSTRATES AN ONCLICK EVENT FOR 'PLACE ORDER' BUTTON
        // IN CASE OF A SINGLE BUTTON, YOU CAN SAVE selectedTabbyProduct UPFRONT WHEN USERS CHOOSES ONE OF THE METHODS
        payLaterButton.onclick = () => {
          // console.log(document.querySelector('#payLaterButton'));
          document.querySelector('#tabby-checkout').style.display = '';
          selectedTabbyProduct = Tabby.PAYLATER;

          // LAUNCH POPUP OR RECREATE AND LAUNCH
          if (relaunchTabby) {
            Tabby.create();
          } else {
            Tabby.launch({product: selectedTabbyProduct});
          }
        };

        installmentsButton.onclick = () => {
          document.querySelector('#tabby-checkout').style.display = '';
          selectedTabbyProduct = Tabby.INSTALLMENTS;

          if (relaunchTabby) {
            Tabby.create();
          } else {
            Tabby.launch({product: selectedTabbyProduct});
          }
        };

        // INITIALIZE SDK AND CREATE POPUP FOR QUICKER DISPLAY WHEN IT'S NEEDED
        Tabby.init(config);
        Tabby.create();
      })();
    </script>
  </body>
</html>