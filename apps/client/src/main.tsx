import React from 'react';
import ReactDOM from 'react-dom/client';
import { App } from './App.js';
import './index.css';

// Suppress noisy Chrome extension port closure errors
window.addEventListener('unhandledrejection', (event) => {
  const reason = event.reason;
  const msg = (reason && (reason.message || reason)) ? String(reason.message || reason) : '';
  if (
    msg.includes('message channel closed before a response was received') ||
    msg.includes('A listener indicated an asynchronous response by returning true')
  ) {
    event.preventDefault();
    event.stopImmediatePropagation();
  }
});

ReactDOM.createRoot(document.getElementById('root')!).render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);
