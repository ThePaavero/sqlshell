(() => {
  const form = document.querySelector('.sql-form')
  let ctrlDown = false
  document.addEventListener('keydown', e => {
    if (e.keyCode === 17) {
      console.log('Ctrl down!')
      ctrlDown = true
    }
    if (e.keyCode === 13 && ctrlDown) {
      form.submit()
    }
  })
  document.addEventListener('keyup', e => {
    if (e.keyCode === 17) {
      ctrlDown = false
    }
  })
})()
