<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Multi-Screen Command Center</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-950 text-slate-200 min-h-screen flex flex-col items-center justify-center p-8">

<h1 class="text-4xl font-bold mb-8">🪟 Multi-Screen Command Center</h1>

<button onclick="enable()" class="px-6 py-3 bg-emerald-500 rounded-xl text-black font-bold hover:scale-105 transition">
    Enable Multi-Screen Access
</button>

<div id="screens" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-10 w-full max-w-4xl"></div>

<script>
    let screenDetails = null

    async function enable(){
        try{
            const perm = await navigator.permissions.query({name:"window-management"})
            if(perm.state!=="granted") await navigator.permissions.request({name:"window-management"})
            screenDetails = await window.getScreenDetails()
            renderScreens()
        }catch(e){
            alert("Permission denied or unsupported browser.")
        }
    }

    function renderScreens(){
        const box = document.getElementById("screens")
        box.innerHTML = ""
        screenDetails.screens.forEach((s,i)=>{
            box.innerHTML += `
      <div class="bg-slate-900 p-6 rounded-xl border border-slate-700">
        <h2 class="text-xl font-bold mb-2">Screen ${i+1}</h2>
        <p>Resolution: ${s.width} x ${s.height}</p>
        <button onclick="openOn(${i})" class="mt-4 w-full bg-blue-500 py-2 rounded-lg font-semibold text-black hover:opacity-80">
          Open Dashboard Here
        </button>
      </div>`
        })
    }

    function openOn(i){
        const s = screenDetails.screens[i]
        const win = window.open("about:blank","_blank",
            `left=${s.left},top=${s.top},width=${s.width},height=${s.height}`)
        win.document.write(`
    <html>
    <body style="margin:0;background:#020617;color:#0f172a;font-family:sans-serif;">
      <div style="display:flex;align-items:center;justify-content:center;height:100vh;background:#020617;color:#34d399;">
        <h1 style="font-size:60px;">LIVE DASHBOARD SCREEN ${i+1}</h1>
      </div>
    </body>
    </html>`)
    }
</script>

</body>
</html>
