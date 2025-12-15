import React from 'react';
import useToggle from '../utils/useToggle';

const Header = () => {
  const [isMenuOpen, toggleMenu] = useToggle(false);

  const navLinks = [
    { id: 'home', label: 'Home', href: '#home' },
    { id: 'resources', label: 'Resources', href: '#resources' },
    { id: 'services', label: 'Services', href: '#services' },
    { id: 'about', label: 'About', href: '#about' },
  ];

  return (
    <header className="header">
      <div className="header-container">
        <div className="logo">
          <h1>Portal</h1>
        </div>
        
        {/* Mobile menu button */}
        <button 
          className={`menu-toggle ${isMenuOpen ? 'active' : ''}`}
          onClick={toggleMenu}
          aria-label={isMenuOpen ? 'Close menu' : 'Open menu'}
        >
          <span></span>
          <span></span>
          <span></span>
        </button>
        
        {/* Navigation */}
        <nav className={`nav ${isMenuOpen ? 'nav-open' : ''}`}>
          <ul>
            {navLinks.map((link) => (
              <li key={link.id}>
                <a href={link.href} onClick={() => isMenuOpen && toggleMenu()}>
                  {link.label}
                </a>
              </li>
            ))}
          </ul>
        </nav>
      </div>
    </header>
  );
};

export default Header;