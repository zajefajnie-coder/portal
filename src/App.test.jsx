import React from 'react';
import { render, screen } from '@testing-library/react';
import App from './App';

test('renders portal header', () => {
  render(<App />);
  const headerElement = screen.getByText(/Portal/i);
  expect(headerElement).toBeInTheDocument();
});

test('renders welcome message', () => {
  render(<App />);
  const welcomeElement = screen.getByText(/Welcome to Portal/i);
  expect(welcomeElement).toBeInTheDocument();
});