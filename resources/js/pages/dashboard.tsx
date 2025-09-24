import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    MessageSquare,
    CheckCircle,
    Activity,
    User
} from 'lucide-react';

interface DashboardProps {
    user: {
        id: number;
        name: string;
        email: string;
        role: 'admin' | 'member';
    };
    stats: {
        sessionsCount: number;
        activeSessionsCount: number;
        completedSessionsCount: number;
    };
}

const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
        opacity: 1,
        transition: {
            staggerChildren: 0.1
        }
    }
};

const itemVariants = {
    hidden: { opacity: 0, y: 20 },
    visible: { opacity: 1, y: 0 }
};

export default function Dashboard({ user, stats }: DashboardProps) {


    return (
        <AppLayout
            header={
                <div className="flex justify-between items-center">
                    <div>
                        <h2 className="text-2xl font-semibold text-gray-900">
                            Welcome back, {user.name}!
                        </h2>
                        <p className="text-gray-600 mt-1">Track your coaching sessions and progress here.</p>
                    </div>
                    <div className="flex items-center gap-3">
                        <Button
                            variant="outline"
                            size="sm"
                            onClick={() => {
                                console.log('🧪 Test button clicked - calling window.profile.chart.show() without parameters');
                                if (window.profile?.chart?.show) {
                                    // Test calling show() without parameters - should use current user
                                    window.profile.chart.show();
                                } else {
                                    console.error('❌ window.profile.chart.show not available');
                                }
                            }}
                        >
                            View PI Chart
                        </Button>
                        <Badge variant={user.role === 'admin' ? 'default' : 'secondary'} className="text-sm">
                            {user.role === 'admin' ? 'Administrator' : 'Member'}
                        </Badge>
                    </div>
                </div>
            }
        >
            <Head title="Dashboard" />

            <motion.div 
                className="space-y-8"
                variants={containerVariants}
                initial="hidden"
                animate="visible"
            >
                {/* Stats Overview */}
                <motion.div variants={itemVariants}>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <Card>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium">Total Sessions</CardTitle>
                                <MessageSquare className="h-4 w-4 text-muted-foreground" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold">{stats.sessionsCount}</div>
                                <p className="text-xs text-muted-foreground">
                                    Coaching conversations
                                </p>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium">Active Sessions</CardTitle>
                                <Activity className="h-4 w-4 text-muted-foreground" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold">{stats.activeSessionsCount}</div>
                                <p className="text-xs text-muted-foreground">
                                    Currently ongoing
                                </p>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium">Completed</CardTitle>
                                <CheckCircle className="h-4 w-4 text-muted-foreground" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold">{stats.completedSessionsCount}</div>
                                <p className="text-xs text-muted-foreground">
                                    Sessions finished
                                </p>
                            </CardContent>
                        </Card>
                    </div>
                </motion.div>

                {/* Quick Actions */}
                <motion.div variants={itemVariants}>
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <MessageSquare className="h-5 w-5" />
                                Start New Session
                            </CardTitle>
                            <CardDescription>
                                Begin a new coaching conversation
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="text-center py-8">
                            <MessageSquare className="h-12 w-12 mx-auto mb-4 text-blue-600" />
                            <p className="text-gray-600 mb-4">
                                Start a conversation in the chat area to begin your coaching journey and create your first session.
                            </p>
                            <Link href="/sessions">
                                <Button>Go to Sessions</Button>
                            </Link>
                        </CardContent>
                    </Card>
                </motion.div>

            </motion.div>
        </AppLayout>
    );
}