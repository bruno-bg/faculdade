from django.http import HttpResponse

def aluno(request):
    return HttpResponse("<h1>Realizado com sucesso a atividade</h1>")
