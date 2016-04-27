# Cough-Monitor
EECE/CS Senior Capstone Project, 2015-2016

Maggie Chapman, Emily Gill, Tara Watson

****************************************************
          1. Introduction
****************************************************

This project was done in collaberation with a project led by German Comina, a PhD candidate at the University of Lima. The Cough Monitor system is aimed to help monitor and treat patients with Tuberculosis. By measuring patient cough rates for 4 hour intervals over 2-4 weeks, doctors can evaluate the effectiveness of treatment. This repository is for the web app where patient information and cough recordings are added and viewed. All patients are identified only by patient code, not name.

****************************************************
          2. System specifics
****************************************************

The web app is currently hosted at www.healthglobalweb.com. This is an Apache server owned by German and located in Peru. The front end of the server is built using bootstrap (www.getbootstrap.com) and jquery. The chartist library is used to display patient data. The backend of the web server is written in php, and the patient information is stored on a MySQL database. 

****************************************************
          3. Matlab cough detection Algorithm
****************************************************

The cough recordings are analyzed using a compiled matlab executable, found at http://healthglobalweb.com/tufts_cough/php/matlab. The runAllWavInDATADIR.exe command analyzes all files in the /DATADIR directory and outputs information in the /DATADIR_proc directory. The first line of the .txt output is a quality flag. 0 : good, 1 : max value was too low, 2 : abnormal variation. The next line in the output file is the length of the input. The third line is the number of cough epochs detected. 

