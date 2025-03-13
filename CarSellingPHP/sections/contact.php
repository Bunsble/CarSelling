<?php
// Contact section content
?>
<div class="contact-us section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="contact-us-content">
                    <div class="row">
                        <div class="row justify-content-center">
                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success">
                                    <?php 
                                    echo $_SESSION['success'];
                                    unset($_SESSION['success']);
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger">
                                    <?php 
                                    echo $_SESSION['error'];
                                    unset($_SESSION['error']);
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                            <form id="contact-form" action="process_contact.php" method="post">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="section-heading">
                                            <h2><em>Contact Us</em> &amp; Get In <span>Touch</span></h2>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <fieldset>
                                            <input type="name" name="name" id="name" placeholder="Your Name..." autocomplete="on" required>
                                        </fieldset>
                                    </div>
                                    <div class="col-lg-6">
                                        <fieldset>
                                            <input type="surname" name="surname" id="surname" placeholder="Your Surname..." autocomplete="on" required>
                                        </fieldset>
                                    </div>
                                    <div class="col-lg-6">
                                        <fieldset>
                                            <input type="text" name="email" id="email" pattern="[^ @]*@[^ @]*" placeholder="Your E-mail..." required="">
                                        </fieldset>
                                    </div>
                                    <div class="col-lg-6">
                                        <fieldset>
                                            <input type="subject" name="subject" id="subject" placeholder="Subject..." autocomplete="on">
                                        </fieldset>
                                    </div>
                                    <div class="col-lg-12">
                                        <fieldset>
                                            <textarea name="message" id="message" placeholder="Message"></textarea>
                                        </fieldset>
                                    </div>
                                    <div class="col-lg-12">
                                        <fieldset>
                                            <button type="submit" id="form-submit" class="orange-button">Send Message Now</button>
                                        </fieldset>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 