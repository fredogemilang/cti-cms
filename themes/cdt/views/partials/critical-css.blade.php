{{-- Lean critical CSS: ~2KB inlined to unblock first paint.
     Contains ONLY what's needed for the mobile above-the-fold skeleton:
     box model reset, body defaults, hero layout, gradient, header bar.
     Full Tailwind CSS loads async (non-blocking) alongside this. --}}
<style>
/* Box model + base reset */
*,*::before,*::after,::backdrop{box-sizing:border-box;border:0 solid;margin:0;padding:0}
html{-webkit-text-size-adjust:100%;line-height:1.5;tab-size:4;-webkit-tap-highlight-color:transparent}
body{font-family:'Inter Variable','Inter',ui-sans-serif,system-ui,sans-serif;-webkit-font-smoothing:antialiased;color:#1a1a1a;background:#fff;overflow-x:hidden}
img,svg,video{display:block;max-width:100%;height:auto;vertical-align:middle}
a{color:inherit;text-decoration:inherit}
h1,h2,h3,h4,h5,h6{font-family:'Prompt',sans-serif;font-size:inherit;font-weight:inherit}
button,[role="button"]{cursor:pointer}
ol,ul,menu{list-style:none}

/* Layout primitives (hero + header) */
.relative{position:relative}.absolute{position:absolute}.fixed{position:fixed}.sticky{position:sticky}
.inset-0{inset:0}.top-0{top:0}.left-0{left:0}.right-0{right:0}.bottom-0{bottom:0}
.z-10{z-index:10}.z-\[100\]{z-index:100}
.flex{display:flex}.inline-flex{display:inline-flex}
.items-center{align-items:center}.justify-center{justify-content:center}.justify-between{justify-content:space-between}
.h-screen{height:100vh}.w-full{width:100%}.h-full{height:100%}
.max-w-xl{max-width:36rem}.max-w-\[1400px\]{max-width:1400px}
.overflow-hidden{overflow:hidden}.overflow-x-hidden{overflow-x:hidden}
.object-cover{object-fit:cover}.origin-center{transform-origin:center}

/* Spacing */
.px-4{padding-left:1rem;padding-right:1rem}.py-3{padding-top:.75rem;padding-bottom:.75rem}
.mb-2{margin-bottom:.5rem}.mb-6{margin-bottom:1.5rem}.mb-8{margin-bottom:2rem}
.gap-2{gap:.5rem}.gap-3{gap:.75rem}

/* Text */
.text-white{color:#fff}.text-xl{font-size:1.25rem;line-height:1.75rem}
.text-4xl{font-size:2.25rem;line-height:2.5rem}
.text-xs{font-size:.75rem;line-height:1rem}.text-sm{font-size:.875rem;line-height:1.25rem}
.text-base{font-size:1rem;line-height:1.5rem}
.font-bold{font-weight:700}.font-light{font-weight:300}.font-semibold{font-weight:600}
.uppercase{text-transform:uppercase}.whitespace-nowrap{white-space:nowrap}
.leading-tight{line-height:1.25}.leading-relaxed{line-height:1.625}
.tracking-wider{letter-spacing:.05em}
.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}
.text-dark{color:#1a1a1a}
.font-body{font-family:'Inter Variable','Inter',ui-sans-serif,system-ui,sans-serif}
.text-white\/90{color:rgb(255 255 255/.9)}

/* Background / gradient (hero) */
.bg-white{background-color:#fff}.bg-primary{background-color:#e30613}
.text-primary{color:#e30613}
.bg-gradient-to-r{background-image:linear-gradient(to right,var(--tw-gradient-stops))}
.from-primary{--tw-gradient-from:#e30613;--tw-gradient-stops:var(--tw-gradient-from),var(--tw-gradient-to,transparent)}
.via-primary\/80{--tw-gradient-via:rgb(227 6 19/.8);--tw-gradient-stops:var(--tw-gradient-from),var(--tw-gradient-via),var(--tw-gradient-to,transparent)}
.to-transparent{--tw-gradient-to:transparent}

/* Borders & misc */
.border-b{border-bottom-width:1px}.border-zinc-100{border-color:#f4f4f5}
.rounded-full{border-radius:9999px}
.transition{transition-property:color,background-color,border-color,text-decoration-color,fill,stroke,opacity,box-shadow,transform,filter,backdrop-filter;transition-timing-function:cubic-bezier(.4,0,.2,1);transition-duration:.15s}

/* Block */
.block{display:block}.hidden{display:none}

/* Responsive: show desktop header, hide mobile header on lg */
@media(min-width:64rem){
  .lg\:hidden{display:none}
  .lg\:flex{display:flex}
  .lg\:px-8{padding-left:2rem;padding-right:2rem}
  .lg\:text-\[54px\]{font-size:54px}
  .lg\:w-3\/4{width:75%}
}
@media(min-width:48rem){
  .md\:text-2xl{font-size:1.5rem;line-height:2rem}
  .md\:text-5xl{font-size:3rem;line-height:1}
  .md\:w-2\/3{width:66.6667%}
}
@media(min-width:40rem){
  .sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}
  .sm\:px-8{padding-left:2rem;padding-right:2rem}
  .sm\:py-3{padding-top:.75rem;padding-bottom:.75rem}
  .sm\:text-sm{font-size:.875rem;line-height:1.25rem}
  .sm\:text-lg{font-size:1.125rem;line-height:1.75rem}
  .sm\:gap-6{gap:1.5rem}
}

/* Lenis overrides (prevent flash of broken sticky) */
html.lenis,html.lenis body{height:auto}
.lenis.lenis-smooth{scroll-behavior:auto!important}
</style>
