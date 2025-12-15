import React from 'react';
import FeatureCard from '../components/FeatureCard';

const HomePage = () => {
  const features = [
    {
      id: 1,
      title: 'Feature 1',
      description: 'Description of key functionality'
    },
    {
      id: 2,
      title: 'Feature 2',
      description: 'Description of another feature'
    },
    {
      id: 3,
      title: 'Feature 3',
      description: 'Additional capabilities'
    }
  ];

  return (
    <main className="homepage">
      <section className="hero">
        <h2>Welcome to Portal</h2>
        <p>Your centralized platform for accessing various resources and services</p>
      </section>
      
      <section className="features">
        {features.map(feature => (
          <FeatureCard 
            key={feature.id}
            title={feature.title}
            description={feature.description}
          />
        ))}
      </section>
    </main>
  );
};

export default HomePage;