import { useState } from 'react';

/**
 * Custom hook for toggling boolean state
 * @param {boolean} initialState - Initial state value
 * @returns {Array} An array containing the current state and toggle function
 */
const useToggle = (initialState = false) => {
  const [state, setState] = useState(initialState);

  const toggle = () => {
    setState(prevState => !prevState);
  };

  return [state, toggle];
};

export default useToggle;