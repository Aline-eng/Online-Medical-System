<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/functions.php';
?>

<section class="faq-section">
    <div class="container">
        <h2>Frequently Asked Questions</h2>
        <p class="lead">Find answers to the most common questions about our online medical system.</p>

        <div class="accordion" id="faqAccordion">

            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            How do I register for an account?
                        </button>
                    </h5>
                </div>
                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#faqAccordion">
                    <div class="card-body">
                        To register, simply click on the "Register" link in the top navigation bar. You will need to provide a username, email, and password. Patients and doctors will also be prompted for additional details after successful registration.
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" id="headingTwo">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            How do I book an appointment?
                        </button>
                    </h5>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#faqAccordion">
                    <div class="card-body">
                        Once you are logged in as a patient, go to your dashboard and click on the "Book Appointment" button. You can then select a doctor, choose an available date and time, and provide the reason for your visit.
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" id="headingThree">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            How can I view my medical records?
                        </button>
                    </h5>
                </div>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#faqAccordion">
                    <div class="card-body">
                        As a logged-in patient, you can view your medical records from your dashboard. Click on the "View Medical Records" button to see all records that have been added by your doctors.
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" id="headingFour">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            What if I need to cancel or reschedule an appointment?
                        </button>
                    </h5>
                </div>
                <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#faqAccordion">
                    <div class="card-body">
                        For now, this feature is not available online. Please contact the doctor's office directly to cancel or reschedule an appointment. We plan to add this functionality in a future update.
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" id="headingFive">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            How do I contact support?
                        </button>
                    </h5>
                </div>
                <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#faqAccordion">
                    <div class="card-body">
                        If you have a question that isn't answered here, please use the "Contact Us" form in the navigation bar. We will respond to your inquiry as soon as possible.
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php 
include_once 'includes/footer.php'; 
?>