const content = {
  en: {
    title: "About Us",
    whoTitle: "Who We Are",
    whoText: "We are a digital healthcare solution provider creating a secure online platform for patients and doctors to upload, share, and access medical reports.",
    missionTitle: "Our Mission",
    missionText: "To simplify medical data management, ensure privacy, and improve healthcare delivery.",
    visionTitle: "Our Vision",
    visionText: "To become Sri Lanka's most reliable digital healthcare portal.",
    footerText: "© 2025 Med Report Portal. All rights reserved."
  },
  si: {
    title: "අපි ගැන",
    whoTitle: "අපි කවුද",
    whoText: "අපි රෝගීන් සහ වෛද්‍යවරුන්ට ඔවුන්ගේ වෛද්‍ය වාර්තා upload, බෙදාගැනීමට සහ බලන්න හැකි ආරක්ෂිත වේදිකාවක් සපයනවා.",
    missionTitle: "අපේ මෙහෙවර",
    missionText: "වෛද්‍ය දත්ත කළමනාකරණය සරල කිරීම, පෞද්ගලිකත්වය ආරක්ෂා කිරීම සහ සෞඛ්‍ය සේවා දියුණු කිරීම.",
    visionTitle: "අපේ දැක්ම",
    visionText: "ශ්‍රී ලංකාවේ විශ්වාසදායකම digital සෞඛ්‍ය වේදිකාව වීම අපේ දැක්මයි.",
    footerText: "© 2025 Med Report Portal. සියලුම හිමිකම් ඇවිරිණි."
  },
  ta: {
    title: "எங்களை பற்றி",
    whoTitle: "நாங்கள் யார்",
    whoText: "நாங்கள் நோயாளிகள் மற்றும் மருத்துவர்கள் தங்களின் மருத்துவ அறிக்கைகளை பதிவேற்ற, பகிர்ந்து கொள்ள மற்றும் பார்க்கக்கூடிய பாதுகாப்பான டிஜிட்டல் தளத்தை வழங்குகிறோம்.",
    missionTitle: "எங்கள் பணி",
    missionText: "மருத்துவ தரவு மேலாண்மையை எளிமைப்படுத்தி, தனியுரிமையை உறுதிப்படுத்தி, சுகாதார சேவையை மேம்படுத்துவதே எங்கள் பணி.",
    visionTitle: "எங்கள் பார்வை",
    visionText: "இலங்கையின் நம்பகமான டிஜிட்டல் சுகாதார தளமாக மாறுவதே எங்கள் பார்வையாகும்.",
    footerText: "© 2025 Med Report Portal. அனைத்து உரிமைகளும் பாதுகாக்கப்பட்டவை."
  }
};

function setLanguage(lang) {
  document.getElementById("title").textContent = content[lang].title;
  document.getElementById("who-title").textContent = content[lang].whoTitle;
  document.getElementById("who-text").textContent = content[lang].whoText;
  document.getElementById("mission-title").textContent = content[lang].missionTitle;
  document.getElementById("mission-text").textContent = content[lang].missionText;
  document.getElementById("vision-title").textContent = content[lang].visionTitle;
  document.getElementById("vision-text").textContent = content[lang].visionText;
  document.getElementById("footer-text").textContent = content[lang].footerText;

  // Active button highlight
  document.querySelectorAll(".lang-btn").forEach(btn => btn.classList.remove("active"));
  event.target.classList.add("active");
}

// Default language
document.addEventListener("DOMContentLoaded", () => setLanguage("en"));
