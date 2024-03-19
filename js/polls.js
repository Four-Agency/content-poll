const pollWrappers = document.querySelectorAll('.poll-wrapper');
pollWrappers.forEach((pollWrapper) => {
  const pollId = pollWrapper.dataset.pollId;
  const cookieName = `poll_${pollId}`;
  const pollSetCookie = Cookies.get(cookieName);

  if (pollSetCookie) {
  }

  pollWrapper.querySelectorAll('.poll-option').forEach((pollOption) => {
    pollOption
      .querySelector('.poll-option-button')
      .addEventListener('click', async (e) => {
        e.preventDefault();
        const optionId = pollOption.dataset.optionId;

        if (pollSetCookie === undefined && pollId && optionId) {
          try {
            const response = await fetch('/poll/submit', {
              headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
              },
              method: 'POST',
              body: JSON.stringify({ pollId, optionId }),
            });
            const result = await response.json();
            if (result.success) {
              pollWrapper.classList.add('voted');
              pollWrapper
                .querySelectorAll('.poll-option-button')
                .forEach((pollOptionButtons) => pollOptionButtons.remove());
              Cookies.set(cookieName, optionId, { expires: 365 });
              pollOption.classList.add('voted');
              pollOption.insertAdjacentHTML(
                'afterbegin',
                '<div class="poll-option-voted-marker">Your selection</div>'
              );
              if (result.completion_text) {
                pollWrapper
                  .querySelector('.field--name-intro-text')
                  .insertAdjacentHTML('afterend', result.completion_text);
                pollWrapper.querySelector('.field--name-intro-text').remove();
              }
            }
          } catch (error) {
            console.log(error);
          }
        }
      });
  });
});
