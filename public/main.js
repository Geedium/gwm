console.log('Registering service worker.')

if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/service-worker.js', { scope: './' })
    .then( (reg) => {
      console.log('Registration succeeded. Scope is ' + reg.scope);
    })
    .catch( (err) => {
      console.log('Registration failed with ' + err);
    })
}