import { Head } from '@inertiajs/react'
import AppLayout from '../layouts/app-layout'

interface ModuleDetailPageProps {
    module: {
        id: number
        title: string
        description: string
        slug: string
        type: string
        topics: string[]
        learning_objectives: string
        expected_outcomes: string
        estimated_duration: number
        difficulty: string
        sample_questions: string[]
        goal: string
        sort_order: number
        is_active: boolean
        users: Array<{
            id: number
            name: string
            email: string
            pivot: {
                assigned_at: string | null
                completed_at: string | null
                progress_data: string | null
            }
        }>
    }
}

export default function ModuleDetailPage({ module }: ModuleDetailPageProps) {
    return (
        <AppLayout>
            <Head title={`Module: ${module.title}`} />

            <div className="py-12">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <h1 className="text-3xl font-bold mb-4">{module.title}</h1>
                            <p className="text-lg mb-6">{module.description}</p>
                            
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 className="text-xl font-semibold mb-2">Module Details</h3>
                                    <ul className="space-y-2">
                                        <li><strong>Type:</strong> {module.type}</li>
                                        <li><strong>Difficulty:</strong> {module.difficulty}</li>
                                        <li><strong>Duration:</strong> {module.estimated_duration} minutes</li>
                                    </ul>
                                </div>
                                
                                <div>
                                    <h3 className="text-xl font-semibold mb-2">Topics Covered</h3>
                                    <ul className="list-disc list-inside space-y-1">
                                        {module.topics.map((topic, index) => (
                                            <li key={index}>{topic}</li>
                                        ))}
                                    </ul>
                                </div>
                            </div>
                            
                            <div className="mt-6">
                                <h3 className="text-xl font-semibold mb-2">Learning Objectives</h3>
                                <p>{module.learning_objectives}</p>
                            </div>
                            
                            <div className="mt-6">
                                <h3 className="text-xl font-semibold mb-2">Expected Outcomes</h3>
                                <p>{module.expected_outcomes}</p>
                            </div>
                            
                            {module.sample_questions && module.sample_questions.length > 0 && (
                                <div className="mt-6">
                                    <h3 className="text-xl font-semibold mb-2">Sample Questions</h3>
                                    <ul className="list-disc list-inside space-y-1">
                                        {module.sample_questions.map((question, index) => (
                                            <li key={index}>{question}</li>
                                        ))}
                                    </ul>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    )
}