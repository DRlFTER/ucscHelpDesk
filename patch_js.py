with open('/Applications/XAMPP/xamppfiles/htdocs/ucscHelpDesk/public/js/ticketFull/ticketFull.js', 'r') as f:
    text = f.read()

import re

# Add staff logic to toggleActionButtons
new_toggle_logic = """
  // Staff: assign/forward/resolve rules
  if (ROLE === "staff") {
    const isPending = !!ticketData.isPending;
    const isAssignedToMe = !!ticketData.isAssignedToMe;
    
    // Hide everything by default
    if (del) del.style.display = "none";
    if (sched) sched.style.display = "none";
    
    const assignBtn = document.getElementById("assignBtn");
    const forwardBtn = document.getElementById("forwardBtn");
    
    if (assignBtn) assignBtn.style.display = isPending ? "" : "none";
    if (forwardBtn) forwardBtn.style.display = isAssignedToMe && !isResolved ? "" : "none";
    if (resolveBtn) resolveBtn.style.display = isAssignedToMe && !isResolved ? "" : "none";
    
    // Also disable chat input if not assigned or resolved
    const sendBtn = document.getElementById("swith open('/Applications/Xnp    text = f.read()

import re

# Add staff logic to toggleActionButtons
new_toggle_logic = """
  // Staff: if
import re

# Add || 
# Add sed)new_toggle_logic = """
  // Staff: assi=   // Staff: assign/foIn  if (ROLE === "staff") {
    const isPif    const isPending = !!is    const isAssignedToMe = !!ticketData.isAshe    
    // Hide everything by default
    if (del) dece    /    if (del) del.style.display =ut    if (sched) sched.style.display = "nconst del = document.getElementById("deleteBtn"  ' t    const forwardBtn = document.getElementById("forwardBtn "    
    if (assignBtn) assignBtn.style.display = isPending t    ed    if (forwardBtn) forwardBtn.style.display = isAssignedToMe && !isme    if (resolveBtn) resolveBtn.style.display = isAssignedToMe && !isResolved ? "" : "none"
     
    // Also disable chat input if not assigned or resolved
    const sendBtn = docume d   =     const sendBtn = document.getElementById("swith open('cu
import re

# Add staff logic to toggleActionButtons
new_toggle_loggetElementById("resolveBtn")
# Add sdecnew_toggle_logic = """
  // Staff: if
ito  // Staff: if
importehimport re

#  a
# Add |ed
# Add seio  // Staff: assi=   // Staff: a =    const isPif    const isPending = !!is    const isAssi
    deleteBtn.addEventListener("click", () => openDeleteModal());
  }

  const assignBtnEl = document    if (del) dece    /    if (deco    if (assignBtn) assignBtn.style.display = isPending t    ed    if (forwardBtn) forwardBtn.style.display = isAssignedToMe && !isme    if (resolveBtn) resolveBtn.style.display = isAssignedToMe && !isResolved ?ib     
    // Also disable chat input if not assigned or resolved
    const sendBtn = docume d   =     const sendBtn = document.getElementById("swith open('cu
import re

# Add staff logic to toggleActionButtons
new_toggle_st    ("    const sendBtn = docume d   =     const sendBtn = docu aimport re

# Add staff logic to toggleActionButtons
new_toggle_loggetElementById("resolveBtns
# Add s awnew_toggle_loggetElementById("resolveBtes# Add sdecnew_toggle_logic = """
  // Sta;
  // Staff: if
ito  // Staff: ialito  // Staffagimportehimport r  
#  a
# Add |ed
  }# Atc# Add se      deleteBtn.addEventListener("click", () => openDeleteModal());
  }

  const assignBtnEl n = docume  }

  const assignBtnEl = document    if (del) dece    /    if 
 
        // Also disable chat input if not assigned or resolved
    const sendBtn = docume d   =     const sendBtn = document.getElementById("swith open('cu
import re

# Add staff logic to toggleActionButtons
new_toggle_st    ("    const sendBtn = docume d   =     const sendBtn = docuar    const sendBtn = docume d   =     const sendBtn = docuorimport re

# Add staff logic to toggleActionButtons
new_toggle_st    ("    const sendBtn = ",
# Add s) => {
      forwardModal.classList.add("ope
# Add staff logic to toggleActionButtons
new_toggle_loggetElementById("resolvedy.classLinew_toggle_loggetElementById("resolveBtt # Add s awnew_toggle_loggetEleme("forwardS  // Sta;
  // Staff: if
ito  // Staff: ialito  // Staffagimportehimport r  
#  a
    // Stat ito  // Staffet#  a
# Add |ed
  }# Atc# Add se      deleteBtn.adda # Awa  }# Atcso  }

  const assignBtnEl n = docume  }

  const assignBtnEl = document    if (del)at
 for
  const assignBtnEl = document  op 
        // Also disable chat input if not assigned or realue    const sendBtn = docume d   =     const se.name;
           import re

# Add staff logic to toggleActionButtons
new_toggle_st    ("    const sendBtn =  }
# Add sconnew_toggle_st    ("    const sendBtn = en
# Add staff logic to toggleActionButtons
new_toggle_st    ("    const sendBtn = ",
# Add s) => {
      forwardModal.classList.add("ope
# Add staocunew_toggle_st    ("    const sendBtn ct")# Add s) => {
      forwardModal.classLi{       forware # Add staff logic to toggleAurn; }
   new_toggle_loggetElementById("resolvedy    // Staff: if
ito  // Staff: ialito  // Staffagimportehimport r  
#  a
    // Stat ito  // Staffet#  a
# Add |ed
  }# Atc# Add se      deByito  // Staffas#  a
    // Stat ito  // Staffet#  a
# Add |ed
  }n'   ea# Add |ed
  }# Atc# Add se        }# Atcon
  const assignBtnEl n = docume  }

  const assignBtnEIdF
  const assignBtnEl = document dy: for
  const as         const data = await re  cso        // Also disable chat input
            import re

# Add staff logic to toggleActionButtons
new_toggle_st    ("    const sendBtn =  }
# Add }
# Add staff logic  { new_toggle_st    ("    const sendBtn =   # Add sconnew_toggle_st    ("    const sme# Add staff logic to toggleActionButtons
new_toggltnnew_toggle_st    ("    const sendBtn = en# Add s) => {
      forwardModal.classLi.classList.remov# Add staocunew_toggle_st    ("    ctt      forwardModal.classLi{       forware # Add staff logic to tomo   new_toggle_loggetElementById("resolvedy    // Staff: if
ito  // Staff: ielito  // Staff: ialito  // Staffagimportehimport r  
#  a
te#  a
    // Stat ito  // Staffet#  a
# Add |ed
  }=>   en# Add |ed
  }# Atc# Add se    ct  }# Atcck    // Stat ito  // Staffet#  a
# Adamppfiles/# Add |ed
  }n'   ea# Add |ed
ti  }n'   /t  }# Atc# Add se  )   cf:
    f.write(text)

