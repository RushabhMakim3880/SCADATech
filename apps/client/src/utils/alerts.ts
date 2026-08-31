import Swal from 'sweetalert2';
import withReactContent from 'sweetalert2-react-content';

const MySwal = withReactContent(Swal);

/**
 * Global Toast configuration for sleek, non-blocking top-right notifications.
 */
export const Toast = MySwal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  background: '#ffffff',
  color: '#0f172a',
  iconColor: '#3b82f6',
  didOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer);
    toast.addEventListener('mouseleave', Swal.resumeTimer);
  },
});

/**
 * Reusable alert functions for the SCADA HMI.
 */
export const HmiAlert = {
  /**
   * Non-blocking success toast.
   */
  success: (title: string) => {
    Toast.fire({
      icon: 'success',
      iconColor: '#10b981', // Emerald green
      title,
    });
  },

  /**
   * Non-blocking error toast.
   */
  error: (title: string) => {
    Toast.fire({
      icon: 'error',
      iconColor: '#ef4444', // Red
      title,
    });
  },

  /**
   * Non-blocking warning toast.
   */
  warning: (title: string) => {
    Toast.fire({
      icon: 'warning',
      iconColor: '#f59e0b', // Amber
      title,
    });
  },

  /**
   * Non-blocking info toast.
   */
  info: (title: string) => {
    Toast.fire({
      icon: 'info',
      iconColor: '#3b82f6', // Blue
      title,
    });
  },

  /**
   * Blocking confirmation modal dialog (e.g., for deleting records).
   */
  confirm: async (title: string, text: string, confirmButtonText = 'Confirm') => {
    const result = await MySwal.fire({
      title,
      text,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3b82f6', // Blue primary
      cancelButtonColor: '#64748b', // Slate cancel
      confirmButtonText,
      background: '#ffffff',
      color: '#0f172a',
      customClass: {
        confirmButton: 'font-bold rounded shadow-sm',
        cancelButton: 'font-bold rounded',
      },
    });
    return result.isConfirmed;
  },
};
