<?php

include 'includes/book-utilities.inc.php';

$customers = readCustomers('data/customers.txt');
$selectedId = isset($_GET['id']) ? trim($_GET['id']) : '';
$selectedCustomer = null;

foreach ($customers as $customer) {
  if ($customer['id'] === $selectedId) {
    $selectedCustomer = $customer;
    break;
  }
}

$orders = array();
if ($selectedCustomer !== null) {
  $orders = readOrders($selectedCustomer['id'], 'data/orders.txt');
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Dc325837 Lei Man Lam</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/material.min.css">
  <link rel="stylesheet" href="css/demo-styles.css">
    <link rel="stylesheet" href="css/styles.css">
    
    <script src="js/material.min.js"></script>
    <script src="js/jquery.sparkline.2.1.2.js"></script>
    
  
</head>

<body>
    
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer
            mdl-layout--fixed-header">
            
    <?php include 'includes/header.inc.php'; ?>
    <?php include 'includes/left-nav.inc.php'; ?>
    
    <main class="mdl-layout__content mdl-color--grey-50">
        <section class="page-content">

            <div class="mdl-grid">

              <!-- mdl-cell + mdl-card -->
              <div class="mdl-cell mdl-cell--7-col card-lesson mdl-card  mdl-shadow--2dp">
                <div class="mdl-card__title mdl-color--orange">
                  <h2 class="mdl-card__title-text">Customers</h2>
                </div>
                <div class="mdl-card__supporting-text">
                    <table class="mdl-data-table  mdl-shadow--2dp">
                      <thead>
                        <tr>
                          <th class="mdl-data-table__cell--non-numeric">Name</th>
                          <th class="mdl-data-table__cell--non-numeric">University</th>
                          <th class="mdl-data-table__cell--non-numeric">City</th>
                          <th>Sales</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($customers as $customer): ?>
                          <tr>
                            <td class="mdl-data-table__cell--non-numeric">
                              <a href="cisc3003-sugex10.php?id=<?php echo urlencode($customer['id']); ?>">
                                <?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?>
                              </a>
                            </td>
                            <td class="mdl-data-table__cell--non-numeric"><?php echo htmlspecialchars($customer['university']); ?></td>
                            <td class="mdl-data-table__cell--non-numeric"><?php echo htmlspecialchars($customer['city']); ?></td>
                            <td>
                              <span class="inlinesparkline"><?php echo htmlspecialchars(str_replace(' ', '', $customer['sales'])); ?></span>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                                              
                      </tbody>
                    </table>
                </div>
              </div>  <!-- / mdl-cell + mdl-card -->
              
              
            <div class="mdl-grid mdl-cell--5-col">
    

       
                  <!-- mdl-cell + mdl-card -->
                  <div class="mdl-cell mdl-cell--12-col card-lesson mdl-card  mdl-shadow--2dp">
                    <div class="mdl-card__title mdl-color--deep-purple mdl-color-text--white">
                      <h2 class="mdl-card__title-text">Customer Details</h2>
                    </div>
                    <div class="mdl-card__supporting-text">
                        <?php if ($selectedCustomer !== null): ?>
                          <h4><?php echo htmlspecialchars($selectedCustomer['first_name'] . ' ' . $selectedCustomer['last_name']); ?></h4>
                          <p><strong>Email:</strong> <?php echo htmlspecialchars($selectedCustomer['email']); ?></p>
                          <p><strong>University:</strong> <?php echo htmlspecialchars($selectedCustomer['university']); ?></p>
                          <p><strong>Address:</strong> <?php echo htmlspecialchars($selectedCustomer['address']); ?></p>
                          <p><strong>City:</strong> <?php echo htmlspecialchars($selectedCustomer['city']); ?></p>
                          <p><strong>Country:</strong> <?php echo htmlspecialchars($selectedCustomer['country']); ?></p>
                          <p><strong>Phone:</strong> <?php echo htmlspecialchars($selectedCustomer['phone']); ?></p>
                        <?php else: ?>
                          <h4>Please select a customer from the table.</h4>
                        <?php endif; ?>
                                                                                                                                                                           
                    </div>    
                  </div>  <!-- / mdl-cell + mdl-card -->   

                  <!-- mdl-cell + mdl-card -->
                  <div class="mdl-cell mdl-cell--12-col card-lesson mdl-card  mdl-shadow--2dp">
                    <div class="mdl-card__title mdl-color--deep-purple mdl-color-text--white">
                      <h2 class="mdl-card__title-text">Order Details</h2>
                    </div>
                    <div class="mdl-card__supporting-text">       
                               
                                                                      

                               <table class="mdl-data-table  mdl-shadow--2dp">
                              <thead>
                                <tr>
                                  <th class="mdl-data-table__cell--non-numeric">Cover</th>
                                  <th class="mdl-data-table__cell--non-numeric">ISBN</th>
                                  <th class="mdl-data-table__cell--non-numeric">Title</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php if ($selectedCustomer === null): ?>
                                  <tr>
                                    <td class="mdl-data-table__cell--non-numeric" colspan="3">Please select a customer to view orders.</td>
                                  </tr>
                                <?php elseif (count($orders) === 0): ?>
                                  <tr>
                                    <td class="mdl-data-table__cell--non-numeric" colspan="3">No order information for this customer.</td>
                                  </tr>
                                <?php else: ?>
                                  <?php foreach ($orders as $order): ?>
                                    <?php
                                      $coverPath = 'images/tinysquare/' . $order['isbn'] . '.jpg';
                                      if (!file_exists($coverPath)) {
                                          $coverPath = 'images/tinysquare/missing.jpg';
                                      }
                                    ?>
                                    <tr>
                                      <td class="mdl-data-table__cell--non-numeric">
                                        <img src="<?php echo htmlspecialchars($coverPath); ?>" alt="<?php echo htmlspecialchars($order['title']); ?>" style="width:40px;height:auto;">
                                      </td>
                                      <td class="mdl-data-table__cell--non-numeric"><?php echo htmlspecialchars($order['isbn']); ?></td>
                                      <td class="mdl-data-table__cell--non-numeric"><?php echo htmlspecialchars($order['title']); ?></td>
                                    </tr>
                                  <?php endforeach; ?>
                                <?php endif; ?>
                              </tbody>
                            </table>
       

                        </div>    
                   </div>  <!-- / mdl-cell + mdl-card -->             


               </div>   
           
           
            </div>  <!-- / mdl-grid -->    

            <footer style="padding: 16px 8px; font-weight: bold;">
              CISC3003 Web Programming: Dc325837 Lei Man Lam 2026
            </footer>

        </section>
    </main>    
</div>    <!-- / mdl-layout --> 

      <script>
      if (window.jQuery && jQuery.fn && jQuery.fn.sparkline) {
        jQuery(function () {
          jQuery('.inlinesparkline').sparkline('html', {
            type: 'bar',
            barColor: '#ff6e40',
            height: '30',
            barWidth: 4,
            barSpacing: 1
          });
        });
      }
      </script>
          
</body>
</html>