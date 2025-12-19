import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import api from '../api/axios';

export default function PortfolioEditor() {
    const { id } = useParams();
    const navigate = useNavigate();
    const isEdit = !!id;

    const [formData, setFormData] = useState({
        title: '',
        description: '',
        is_public: true,
        tags: [],
    });
    const [images, setImages] = useState([]);
    const [existingImages, setExistingImages] = useState([]);
    const [tagInput, setTagInput] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    useEffect(() => {
        if (isEdit) {
            loadPortfolio();
        }
    }, [id]);

    const loadPortfolio = async () => {
        try {
            const response = await api.get(`/my-portfolios`);
            const portfolio = response.data.find(p => p.id === parseInt(id));
            if (portfolio) {
                setFormData({
                    title: portfolio.title,
                    description: portfolio.description || '',
                    is_public: portfolio.is_public,
                    tags: portfolio.tags || [],
                });
                setExistingImages(portfolio.images || []);
            }
        } catch (error) {
            console.error('Error loading portfolio:', error);
            setError('Failed to load portfolio');
        }
    };

    const handleImageChange = (e) => {
        const files = Array.from(e.target.files);
        setImages([...images, ...files]);
    };

    const removeImage = (index) => {
        setImages(images.filter((_, i) => i !== index));
    };

    const removeExistingImage = async (imageId) => {
        try {
            // In a real app, you'd have an endpoint to delete individual images
            // For now, we'll just remove it from the UI
            setExistingImages(existingImages.filter(img => img.id !== imageId));
        } catch (error) {
            console.error('Error removing image:', error);
        }
    };

    const handleAddTag = (e) => {
        if (e.key === 'Enter' && tagInput.trim()) {
            e.preventDefault();
            const tagName = tagInput.trim();
            // Sanitize tag (prevent XSS)
            const sanitizedTag = tagName.replace(/[<>]/g, '');
            if (!formData.tags.find(t => t.toLowerCase() === sanitizedTag.toLowerCase())) {
                setFormData({
                    ...formData,
                    tags: [...formData.tags, sanitizedTag],
                });
            }
            setTagInput('');
        }
    };

    const removeTag = (index) => {
        setFormData({
            ...formData,
            tags: formData.tags.filter((_, i) => i !== index),
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setLoading(true);

        try {
            const submitData = new FormData();
            submitData.append('title', formData.title);
            submitData.append('description', formData.description);
            submitData.append('is_public', formData.is_public ? '1' : '0');
            
            formData.tags.forEach(tag => {
                submitData.append('tags[]', tag);
            });

            images.forEach((image) => {
                submitData.append('images[]', image);
            });

            if (isEdit) {
                await api.put(`/portfolios/${id}`, submitData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });
            } else {
                await api.post('/portfolios', submitData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });
            }

            navigate('/my-portfolios');
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to save portfolio');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="max-w-4xl mx-auto">
            <h1 className="text-3xl font-bold mb-6">
                {isEdit ? 'Edit Portfolio' : 'Create New Portfolio'}
            </h1>

            {error && (
                <div className="alert alert-error mb-4">
                    <span>{error}</span>
                </div>
            )}

            <form onSubmit={handleSubmit} className="card bg-base-100 shadow-xl">
                <div className="card-body">
                    <div className="form-control mb-4">
                        <label className="label">
                            <span className="label-text">Title *</span>
                        </label>
                        <input
                            type="text"
                            className="input input-bordered"
                            value={formData.title}
                            onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                            required
                        />
                    </div>

                    <div className="form-control mb-4">
                        <label className="label">
                            <span className="label-text">Description</span>
                        </label>
                        <textarea
                            className="textarea textarea-bordered"
                            value={formData.description}
                            onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                            rows={4}
                        />
                    </div>

                    <div className="form-control mb-4">
                        <label className="label cursor-pointer">
                            <span className="label-text">Make portfolio public</span>
                            <input
                                type="checkbox"
                                className="toggle toggle-primary"
                                checked={formData.is_public}
                                onChange={(e) => setFormData({ ...formData, is_public: e.target.checked })}
                            />
                        </label>
                    </div>

                    <div className="form-control mb-4">
                        <label className="label">
                            <span className="label-text">Tags</span>
                        </label>
                        <input
                            type="text"
                            className="input input-bordered"
                            placeholder="Press Enter to add tag"
                            value={tagInput}
                            onChange={(e) => setTagInput(e.target.value)}
                            onKeyPress={handleAddTag}
                        />
                        {formData.tags.length > 0 && (
                            <div className="flex flex-wrap gap-2 mt-2">
                                {formData.tags.map((tag, index) => (
                                    <span key={index} className="badge badge-primary gap-2">
                                        {tag}
                                        <button
                                            type="button"
                                            onClick={() => removeTag(index)}
                                            className="btn btn-xs btn-circle"
                                        >
                                            ×
                                        </button>
                                    </span>
                                ))}
                            </div>
                        )}
                    </div>

                    <div className="form-control mb-4">
                        <label className="label">
                            <span className="label-text">Images *</span>
                        </label>
                        <input
                            type="file"
                            className="file-input file-input-bordered w-full"
                            accept="image/*"
                            multiple
                            onChange={handleImageChange}
                            required={!isEdit || images.length === 0}
                        />
                        
                        {existingImages.length > 0 && (
                            <div className="grid grid-cols-3 gap-4 mt-4">
                                {existingImages.map((image) => (
                                    <div key={image.id} className="relative">
                                        <img
                                            src={`/storage/${image.image_path}`}
                                            alt={image.alt_text}
                                            className="w-full h-32 object-cover rounded"
                                        />
                                        <button
                                            type="button"
                                            onClick={() => removeExistingImage(image.id)}
                                            className="btn btn-xs btn-error absolute top-2 right-2"
                                        >
                                            ×
                                        </button>
                                    </div>
                                ))}
                            </div>
                        )}

                        {images.length > 0 && (
                            <div className="grid grid-cols-3 gap-4 mt-4">
                                {images.map((image, index) => (
                                    <div key={index} className="relative">
                                        <img
                                            src={URL.createObjectURL(image)}
                                            alt={`Preview ${index + 1}`}
                                            className="w-full h-32 object-cover rounded"
                                        />
                                        <button
                                            type="button"
                                            onClick={() => removeImage(index)}
                                            className="btn btn-xs btn-error absolute top-2 right-2"
                                        >
                                            ×
                                        </button>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>

                    <div className="card-actions justify-end">
                        <button
                            type="button"
                            onClick={() => navigate('/my-portfolios')}
                            className="btn btn-ghost"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            className="btn btn-primary"
                            disabled={loading}
                        >
                            {loading ? (
                                <span className="loading loading-spinner"></span>
                            ) : (
                                isEdit ? 'Update Portfolio' : 'Create Portfolio'
                            )}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    );
}

