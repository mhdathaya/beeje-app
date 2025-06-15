// public/js/supabase.js

import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js/+esm'

// Ganti dengan project kamu
const supabaseUrl = 'https://bkxvujwirtjfgiyympfl.supabase.co'
const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImJreHZ1andpcnRqZmdpeXltcGZsIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDU0MTY0MjIsImV4cCI6MjA2MDk5MjQyMn0.DKtUj-9TcNdVhzonfbKiistGku7b5MW_0g83Lmok8Wo' // aman digunakan di frontend
export const supabase = createClient(supabaseUrl, supabaseKey)

export async function signInWithGoogle() {
  const { error } = await supabase.auth.signInWithOAuth({
    provider: 'google',
    options: {
      redirectTo: window.location.origin + '/admin/dashboard' // ganti dengan halaman setelah login
    }
  })
  if (error) {
    alert('Login gagal: ' + error.message)
  }
}
