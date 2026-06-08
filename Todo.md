We are Creating a BLOOD BANK MANAGEMENT SYSTEM using php,MySQL,Tailwind css as the tech stack.
The folder structure is an MVC(Model,Views,Controllers).
Ideally we shall have three user types:the system admin,the hospital manager(REPRESENTING THE HOSPITAL),The donors.
The days objective:
 1. create a landing page for donors and the hospital managers to sign up and login.
 2. create a dashboard for the system admin to manage the system.
 3. create a dashboard for the hospital manager to manage the hospital.
 4. create a dashboard for the donors to manage their donations.
 5. Ensure authentication and authorization for all user types.
 6. A registration and login for all user types.
 Strictly use the design layout provided in the DesignLayout.md file.
 and make neccesary changes to the schema to align with todays goal.
 Make sure to strictly adhere to the folder structure. 



 DONORS PROMPT
 Implement the donors page on the admin dashboard. The page should display all the donors in the system. Each donor should have a button to view their profile, edit their profile, delete their profile, and a button to view their donation history.
 It should have stats for the donors, such as the total number of donors, the number of active donors, the number of inactive donors, the number of donors who have donated blood, etc.
 Make sure all buttons work and controllers are updated to provide functionality to the page. use the design layout provided in the DesignLayout.md file as a reference for the design.Also have a way to search and filter the donors based on their blood type, eligibility status, and donation history. The search and filter should be done in real-time as the user types or selects an option from the dropdown menu. and also ensure that the page is responsive and works on all devices. 
 Also have a button to add a new donor at the top right of the page, which should open a modal to add a new donor. The modal should have the same fields as the donors table.
 

 HOSPITAL PROMPT 
 Implement the hospital management page on the admin dashboard. The page should display all the hospitals in the system. Each hospital should have a button to view their profile, edit their profile, delete their profile, and a button to view their donation history.
 It should have stats for the hospitals, such as the total number of hospitals, the number of active hospitals, the number of inactive hospitals, the number of hospitals that have donated blood, etc.
 Make sure all buttons work and controllers are updated to provide functionality to the page. use the design layout provided in the DesignLayout.md file as a reference for the design.Also have a way to search and filter the hospitals based on their blood type, eligibility status, and donation history. The search and filter should be done in real-time as the user types or selects an option from the dropdown menu. and also ensure that the page is responsive and works on all devices. 
 Also have a button to add a new hospital at the top right of the page, which should open a modal to add a new hospital. The modal should have the same fields as the hospital table.
 
 H0SPITAL MANAGER
 On the hospital manager page lets complete the blood request page first then move to inventory page.

 BLOOD REQUEST PAGE [COMPLETED]
The hospital manager should be able to create blood requests.The blood request page should display all the blood requests in the system. Each blood request should have a button to view their profile, edit their profile, delete their profile, and a button to view their donation history.
It should have stats for the blood requests, such as the total number of blood requests, the number of active blood requests, the number of inactive blood requests, the number of blood requests that have been fulfilled, etc.
Make sure all buttons work and controllers are updated to provide functionality to the page. use the design layout provided in the DesignLayout.md file as a reference for the design.Also have a way to search and filter the blood requests based on their blood type, eligibility status, and donation history. The search and filter should be done in real-time as the user types or selects an option from the dropdown menu. and also ensure that the page is responsive and works on all devices. 
Also have a button to add a new blood request at the top right of the page, which should open a modal to add a new blood request. The modal should have the same fields as the blood request table.

DONORS SIDE.
on the dashboard make sure the schedule button takes you to the appointments page.
make sure the appointments page is implemented correctly with a working calendar.
the calendar should allow you to select a date and time for the appointment.
the appointment should be booked with a hospital.
(Only Donors can reschedule not hospital managers, This should be implimented in the appointments.php make sure all the links/routing for this functionality works and update their respective controllers and models as well)
also the donors history should be implemented
and the profile page implemented as well.
the donr should be able to edit their profile and save the changes.


ADMIN SIDE.
On the hospital page on the admins dahboard lets ensure that the hospital name has a hyperlink that takes you to the hospital profile page.the profile should be viewable by the admin. it should have a list of all the blood requests made by that hospital, a list of all the appointments made by that hospital, a list of all the inventory of that hospital, etc. 
In the hospital profile on the admins dashboard ensure that the list of blood requests made by that hospital is clickable and takes you to the blood request page for that specific blood request.ensure that all the buttons work, use the design layout provided in the DesignLayout.md file as a reference for the design and also ensure that the page is responsive and works on all devices.the admin should have the ability to fulfill the requetsmade by hospitals and also the ability to delete the appointments made by hospitals.Also see the hospitals registered.On the hospital profile page there should be a section for stats for each hospital, such as the total number of blood requests, the number of active blood requests, the number of inactive blood requests, the number of blood requests that have been fulfilled.The page should show the contact info, address, email, contact person, phone number, type of hospital and display the urgency level of the blood request, which can be  'low','medium','high','critical' and when it was requested.The hospital manager in charge of the making the requests should be able to update the status of the blood request to fulfilled and also the ability to delete the blood request and their contact info made available to the admin.Dont put unnecessary KPI's on the page.


Admin correction mode;
On the hospital profile page where the admin is able to view the blood request details.The admin should be able to choose the number of units that are required to be dispatched to the hospital based on their request  if the units are available in the inventory  the units should be dispatched immediately and the status of the blood request updated whether fulfilled,pending,partially fulfilled or rejected
If the units are not available the status of the blood request should be updated to pending and the hospital manager should be notified.
use the design layout provided in the DesignLayout.md file as a reference for the design  ignoring the accents , also ensure that the page is responsive and works on all devices.